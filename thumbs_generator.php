<?php
//
// thumbs_generator, its page in the control panel: ?cp=thumbs_generator
//

// not for directly open
if (!defined('IN_ADMIN')) {
    exit();
}

//current template
$stylee = 'admin_thumbs_generator';
$styleePath = __DIR__;

// template variables
$action = basename(ADMIN_PATH) . '?cp=thumbs_generator';
$thumbs_generator_library = thumbs_generator_library();

// the key of a link, not of a form: it is good for an hour or two, and the page sends it again with every step
$thumbs_generator_action = $action . '&amp;case=generate&amp;' . kleeja_add_form_key_get('THUMBS_GENERATOR');

/**
 * make the missing thumbnails: the page asks for one short step after the other (_ajax_),
 * without JavaScript the form makes one longer step and tells how far it went
 */
if (g('case') === 'generate') {
    $thumbs_generator_json = ig('_ajax_');
    $thumbs_generator_error = '';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !kleeja_check_form_key_get('THUMBS_GENERATOR')) {
        $thumbs_generator_error = $lang['INVALID_GET_KEY'];
    } elseif ($thumbs_generator_library === '') {
        $thumbs_generator_error = $olang['THUMBS_GENERATOR_NO_LIBRARY'];
    }

    if ($thumbs_generator_error !== '') {
        if ($thumbs_generator_json) {
            echo_array_ajax(['error' => $thumbs_generator_error]);
        }

        kleeja_admin_err($thumbs_generator_error, $action);
    }

    if ($thumbs_generator_json) {
        echo_array_ajax(thumbs_generator_step(max(0, p('after', 'int')), THUMBS_GENERATOR_STEP_SECONDS));
    }

    // from the first image: the ones that have their thumbnail already are skipped quickly
    $thumbs_generator_result = thumbs_generator_step(0, THUMBS_GENERATOR_FORM_SECONDS);
    $thumbs_generator_counts = '';
    $thumbs_generator_notes = '';

    foreach (['created', 'existing', 'missing', 'failed'] as $count) {
        $thumbs_generator_counts .=
            '<li>' .
            $olang['THUMBS_GENERATOR_' . strtoupper($count)] .
            ': ' .
            $thumbs_generator_result[$count] .
            '</li>';

        // why some images have no thumbnail, as the page tells it with JavaScript
        if (in_array($count, ['missing', 'failed'], true) && $thumbs_generator_result[$count] > 0) {
            $thumbs_generator_notes .= '<p>' . $olang['THUMBS_GENERATOR_' . strtoupper($count) . '_NOTE'] . '</p>';
        }
    }

    kleeja_admin_info(
        '<p>' .
            $olang[$thumbs_generator_result['done'] ? 'THUMBS_GENERATOR_DONE' : 'THUMBS_GENERATOR_PARTIAL'] .
            '</p><ul>' .
            $thumbs_generator_counts .
            '</ul>' .
            $thumbs_generator_notes .
            '<p><a class="alert-link" href="' .
            $action .
            '">' .
            $olang['R_THUMBS_GENERATOR'] .
            '</a></p>',
    );
}

//
// the page
//
$thumbs_generator_total = thumbs_generator_count_images();
[$thumbs_generator_width, $thumbs_generator_height] = thumbs_generator_dimensions();
$thumbs_generator_can_run = $thumbs_generator_library !== '' && $thumbs_generator_total > 0;

// the folder of new uploads, the folders of older files are checked image by image
$thumbs_generator_folder = trim($config['foldername'], '/') . '/thumbs';
$thumbs_generator_folder_note = is_writable(PATH . $thumbs_generator_folder)
    ? ''
    : sprintf($olang['THUMBS_GENERATOR_NO_FOLDER'], '<code dir="ltr">' . $thumbs_generator_folder . '</code>');

$thumbs_generator_script =
    PATH . KLEEJA_PLUGINS_FOLDER . '/thumbs_generator/assets/thumbs_generator.js?v=' . THUMBS_GENERATOR_VERSION;
