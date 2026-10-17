/*
 * thumbs_generator, the Generate button of ?cp=thumbs_generator.
 * The images are checked in short steps, each one goes on from the last image of the one before,
 * so the time limit of PHP never stops a big site, and the page shows how far the generator is.
 * Without this script the form still works, one longer step at a time.
 */
(function () {
    "use strict";

    var root = document.querySelector("[data-tg]");
    var form = root && root.querySelector("[data-tg-form]");

    if (!form || !window.fetch || !window.FormData) {
        return;
    }

    var button = form.querySelector('button[type="submit"]');
    var label = form.querySelector("[data-tg-label]");
    var progress = root.querySelector("[data-tg-progress]");
    var state = root.querySelector("[data-tg-state]");
    var checked = root.querySelector("[data-tg-checked]");
    var meter = root.querySelector("[data-tg-meter]");
    var bar = root.querySelector("[data-tg-bar]");
    var result = root.querySelector("[data-tg-result]");
    var total = parseInt(root.dataset.tgTotal, 10) || 0;
    var counts = { created: 0, existing: 0, missing: 0, failed: 0 };
    var checkedCount = 0;
    var lastId = 0;
    var running = false;

    // %1$s, %2$s .. of the translated words
    function format(text, values) {
        return String(text || "").replace(/%(\d)\$s/g, function (match, n) {
            return values[n - 1];
        });
    }

    function show() {
        var share = total ? Math.min(checkedCount / total, 1) : 1;
        var percent = Math.round(share * 100);

        bar.style.width = (share * 100).toFixed(1) + "%";
        meter.setAttribute("aria-valuenow", percent);
        checked.textContent = format(root.dataset.tgMsgProgress, [checkedCount, total]) + " (" + percent + "%)";

        Object.keys(counts).forEach(function (name) {
            root.querySelector('[data-tg-count="' + name + '"]').textContent = counts[name];
        });
    }

    // the generator stopped: the message is written with textContent only, a line for each text
    function stop(type, lines) {
        running = false;
        bar.classList.remove("progress-bar-animated");
        state.classList.add("d-none");

        result.className = "alert alert-" + type + " mt-4 mb-0";
        result.textContent = "";

        lines.forEach(function (line, index) {
            var p = document.createElement("p");
            p.className = index === lines.length - 1 ? "mb-0" : "mb-2";
            p.textContent = line;
            result.appendChild(p);
        });
    }

    // every image is checked, there is nothing left to do until the page is opened again
    function finish() {
        var lines = [root.dataset.tgMsgDone];

        if (counts.failed) {
            lines.push(root.dataset.tgMsgFailed);
        }

        if (counts.missing) {
            lines.push(root.dataset.tgMsgMissing);
        }

        bar.classList.remove("progress-bar-striped");
        stop(counts.failed ? "warning" : "success", lines);
    }

    // an error, or an ended session: the button goes on from the last image that was checked
    function fail(message) {
        bar.classList.add("bg-danger");
        stop("danger", [message || root.dataset.tgMsgError]);
        label.textContent = root.dataset.tgMsgContinue;
        button.disabled = false;
    }

    function step() {
        var body = new FormData();
        body.append("after", lastId);

        fetch(form.action + "&_ajax_=1", {
            method: "POST",
            body: body,
            credentials: "same-origin",
            cache: "no-store",
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
            .then(function (response) {
                // the control panel answers 401 when the session has ended
                if (response.status === 401) {
                    throw new Error(root.dataset.tgMsgSession);
                }

                return response.json().catch(function () {
                    return null;
                });
            })
            .then(function (data) {
                if (!data || data.error || typeof data.last_id !== "number") {
                    throw new Error((data && data.error) || "");
                }

                lastId = data.last_id;
                checkedCount += data.checked;

                Object.keys(counts).forEach(function (name) {
                    counts[name] += data[name];
                });

                show();

                if (data.done) {
                    finish();
                } else {
                    step();
                }
            })
            .catch(function (error) {
                fail(error && error.message);
            });
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        if (running) {
            return;
        }

        running = true;
        button.disabled = true;
        bar.classList.remove("bg-danger");
        bar.classList.add("progress-bar-animated");
        state.classList.remove("d-none");
        progress.classList.remove("d-none");
        result.classList.add("d-none");

        show();
        step();
    });

    // closing the page stops the generator after the current step, so the browser asks first
    window.addEventListener("beforeunload", function (event) {
        if (running) {
            event.preventDefault();
            event.returnValue = root.dataset.tgMsgLeave;
        }
    });
})();
