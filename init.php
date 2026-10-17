<?php
// Kleeja Plugin
// thumbs_generator
// Version: 2.0
// Developer: Kleeja team

// Prevent illegal run
if (!defined('IN_PLUGINS_SYSTEM')) {
    exit();
}

// Plugin Basic Information
$kleeja_plugin['thumbs_generator']['information'] = [
    // The casual name of this plugin, anything can a human being understands
    'plugin_title' => [
        'en' => 'Thumbnail Generator',
        'ar' => 'مولّد الصور المصغّرة',
    ],
    // Who wrote this plugin?
    'plugin_developer' => 'Kleeja.net',
    // This plugin version
    'plugin_version' => '2.0',
    // Explain what is this plugin, why should I use it?
    'plugin_description' => [
        'en' =>
            'Create the missing thumbnails of images that are already uploaded, in one click from the control panel.',
        'ar' => 'تنشئ الصور المصغّرة الناقصة للصور المرفوعة من قبل، بنقرة واحدة من لوحة التحكم.',
    ],
    //settings page, if there is one (what after ? like cp=j_plugins)
    'settings_page' => 'cp=thumbs_generator',
    // Min version of Kleeja that's requiered to run this plugin
    'plugin_kleeja_version_min' => '4.0.0',
    // Max version of Kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => '4.9',
    // Should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 0,
];

//after installation message, you can remove it, it's not requiered
$kleeja_plugin['thumbs_generator']['first_run']['ar'] = "
أضافت هذه الإضافة صفحة «مولّد الصور المصغّرة» إلى لوحة التحكم. اضغط فيها «إنشاء الصور المصغّرة الناقصة»،
فتُنشأ صورة مصغّرة لكل صورة مرفوعة ليست لها واحدة، بالأبعاد المحددة في إعدادات التحميل.
<br>
<a href='./index.php?cp=thumbs_generator'>مولّد الصور المصغّرة</a> -
<a href='./index.php?cp=s_help#help-thumbs_generator'>دليل الإضافة</a> -
<a href='https://github.com/kleeja/thumbs_generator/issues' target='_blank' rel='noopener'>الإبلاغ عن مشكلة</a>
";

$kleeja_plugin['thumbs_generator']['first_run']['en'] = "
This plugin added the Thumbnail Generator page to the control panel. Click Generate missing thumbnails there,
and every uploaded image that has no thumbnail gets one, in the size set in Upload settings.
<br>
<a href='./index.php?cp=thumbs_generator'>Thumbnail Generator</a> -
<a href='./index.php?cp=s_help#help-thumbs_generator'>Plugin guide</a> -
<a href='https://github.com/kleeja/thumbs_generator/issues' target='_blank' rel='noopener'>Report a problem</a>
";

// Plugin Installation function
$kleeja_plugin['thumbs_generator']['install'] = function ($plg_id) {
    foreach (thumbs_generator_translations() as $lang_id => $words) {
        add_olang($words, $lang_id, $plg_id);
    }
};

// 1.0 had no words, 2.0 has its page in the control panel. The words are written again, so a new version brings
// its new words, with queries of its own: add_olang(), delete_olang() and delete_cache() run hooks,
// and the plugins are still loading
$kleeja_plugin['thumbs_generator']['update'] = function ($old_version, $new_version) {
    global $SQL, $dbprefix;

    $result = $SQL->build([
        'SELECT' => 'plg_id',
        'FROM' => "{$dbprefix}plugins",
        'WHERE' => 'plg_name = :name',
        'BIND' => ['name' => 'thumbs_generator'],
    ]);

    $row = $SQL->fetch_array($result);
    $plg_id = (int) ($row['plg_id'] ?? 0);
    $SQL->freeresult($result);

    //a plg_id of 0 would delete the words of the core and of every plugin
    if ($plg_id > 0) {
        $SQL->build([
            'DELETE' => "{$dbprefix}lang",
            'WHERE' => 'plg_id = :plg_id',
            'BIND' => ['plg_id' => $plg_id],
        ]);

        foreach (thumbs_generator_translations() as $lang_id => $words) {
            foreach ($words as $word => $trans) {
                $SQL->build([
                    'INSERT' => 'word, trans, lang_id, plg_id',
                    'INTO' => "{$dbprefix}lang",
                    'VALUES' => ':word, :trans, :lang_id, :plg_id',
                    'BIND' => ['word' => $word, 'trans' => $trans, 'lang_id' => $lang_id, 'plg_id' => $plg_id],
                ]);
            }
        }
    }

    // the cached words, and the compiled page of the control panel:
    // an update doesn't delete the compiled templates, the page is compiled again when it is opened
    $cached = array_merge(
        glob(PATH . 'cache/data_lang*.php') ?: [],
        glob(PATH . 'cache/tpl_admin_thumbs_generator*.php') ?: [],
    );

    foreach ($cached as $file) {
        @unlink($file);
    }
};

// Plugin Uninstallation, function to be called at unistalling.
// The thumbnails that the plugin made stay, they are thumbnails like the ones of the uploader
$kleeja_plugin['thumbs_generator']['uninstall'] = function ($plg_id) {
    //a plg_id of 0 would delete the words of the core and of every plugin
    if ((int) $plg_id > 0) {
        foreach (array_keys(thumbs_generator_translations()) as $lang_id) {
            delete_olang(null, $lang_id, (int) $plg_id);
        }
    }
};

// Plugin functions
$kleeja_plugin['thumbs_generator']['functions'] = [
    //add to admin menu
    'begin_admin_page' => function ($args) {
        $adm_extensions = $args['adm_extensions'];
        $ext_icons = $args['ext_icons'];

        $adm_extensions[] = 'thumbs_generator';
        $ext_icons['thumbs_generator'] = 'images';

        // the words of the plugin in the language of the admin, then in English,
        // for a site where they are missing from the language table
        $olang = (array) ($args['olang'] ?? []) + thumbs_generator_words();

        return compact('adm_extensions', 'ext_icons', 'olang');
    },

    //add as admin page to reach when click on admin menu item we added.
    'not_exists_thumbs_generator' => function () {
        $include_alternative = __DIR__ . '/thumbs_generator.php';

        return compact('include_alternative');
    },

    // the guide of the plugin on the help page of the control panel, its words are in language/help_{code}.php
    'admin_help_guides' => function ($args) {
        $help_guides = $args['help_guides'];
        $words = thumbs_generator_help_words();

        // the name of the plugin is the key, so Kleeja knows that the plugin has its guide,
        // and the help button of the page of the plugin opens it
        $help_guides['thumbs_generator'] = [
            'group' => 'plugins',
            'icon' => 'images',
            'title' => $words['THUMBS_GENERATOR_HELP_TITLE'],
            'intro' => $words['THUMBS_GENERATOR_HELP_INTRO'],
            'page' => 'thumbs_generator',
            'link' => './?cp=thumbs_generator',
            // tips and warnings are shown beside the others on wide screens
            'sections' => [
                thumbs_generator_help_section($words, 'features', 'FEATURE'),
                thumbs_generator_help_section($words, 'steps', 'STEP'),
                thumbs_generator_help_section($words, 'faq', 'FAQ'),
                thumbs_generator_help_section($words, 'tips', 'TIP'),
                thumbs_generator_help_section($words, 'warnings', 'WARNING'),
            ],
        ];

        // the guide of Image control, the page that shows the thumbnails, points to the plugin
        if (isset($help_guides['images'])) {
            $help_guides['images']['sections'][] = ['type' => 'text', 'text' => $words['THUMBS_GENERATOR_HELP_NOTE']];
        }

        return compact('help_guides');
    },
];

/**
 * special functions
 */

if (!function_exists('thumbs_generator_step')) {
    // the version of the files of the plugin, it renews the browser cache of its script
    define('THUMBS_GENERATOR_VERSION', '2.0');
    // the extensions of the images that get a thumbnail, as the Image control page of Kleeja lists them
    define('THUMBS_GENERATOR_TYPES', ['gif', 'jpg', 'png', 'bmp', 'jpeg', 'GIF', 'JPG', 'PNG', 'BMP', 'JPEG']);
    // the images that are read from the database at a time
    define('THUMBS_GENERATOR_ROWS', 100);
    // the seconds of one step of the page, which then asks for the next one
    define('THUMBS_GENERATOR_STEP_SECONDS', 4);
    // without JavaScript, the form makes one longer step, under the 30 seconds that PHP allows by default
    define('THUMBS_GENERATOR_FORM_SECONDS', 20);

    /**
     * the number of uploaded images, the ones that the generator checks
     * @return int
     */
    function thumbs_generator_count_images()
    {
        global $SQL, $dbprefix;

        $result = $SQL->build([
            'SELECT' => 'COUNT(id) AS total',
            'FROM' => "{$dbprefix}files",
            'WHERE' => 'type IN (:types)',
            'BIND' => ['types' => THUMBS_GENERATOR_TYPES],
        ]);

        $row = $SQL->fetch_array($result);
        $SQL->freeresult($result);

        return (int) ($row['total'] ?? 0);
    }

    /**
     * the size of the thumbnails, read from the setting of Upload settings as the uploader of Kleeja reads it
     * @return int[] the width and the height
     */
    function thumbs_generator_dimensions()
    {
        global $config;

        $dimensions = explode('*', (string) ($config['thmb_dims'] ?? ''));

        if (count($dimensions) !== 2) {
            return [150, 150];
        }

        return [(int) trim($dimensions[0]), (int) trim($dimensions[1])];
    }

    /**
     * the library that makes the thumbnails: helper_thumb() of Kleeja uses Imagick when PHP has it, GD otherwise
     * @return string empty when PHP has neither
     */
    function thumbs_generator_library()
    {
        if (function_exists('phpversion') && phpversion('imagick')) {
            return 'Imagick';
        }

        return function_exists('imagecreatefromjpeg') ? 'GD' : '';
    }

    /**
     * whether GD can open the image: it holds every pixel in the memory of PHP, and an image that is too big
     * for memory_limit would end the whole step with a fatal error, the same image again at every try.
     * Imagick works outside that limit
     * @param  string $path
     * @return bool
     */
    function thumbs_generator_fits_in_memory($path)
    {
        if (thumbs_generator_library() !== 'GD') {
            return true;
        }

        $limit = trim((string) ini_get('memory_limit'));

        if ($limit === '' || $limit === '-1') {
            return true;
        }

        $size = @getimagesize($path);

        // not an image that GD can read
        if (empty($size[0]) || empty($size[1])) {
            return false;
        }

        $bytes = (int) $limit * (['k' => 1024, 'm' => 1048576, 'g' => 1073741824][strtolower(substr($limit, -1))] ?? 1);

        // about 5 bytes for each pixel of the image, and 4 MB for the thumbnail and the rest
        return memory_get_usage() + $size[0] * $size[1] * 5 + 4194304 < $bytes;
    }

    /**
     * makes the thumbnail of one image, when it has none
     * @param  array  $image  a row of the files table
     * @param  int    $width
     * @param  int    $height
     * @return string what happened, a key of the counts of a step: created, existing, missing or failed
     */
    function thumbs_generator_image($image, $width, $height)
    {
        $name = (string) $image['name'];
        $folder = PATH . $image['folder'];

        // the names are written by Kleeja, a name that is a path would save the thumbnail somewhere else
        if ($name === '' || basename($name) !== $name || !is_file("{$folder}/{$name}")) {
            return 'missing';
        }

        $thumbnail = "{$folder}/thumbs/{$name}";

        if (file_exists($thumbnail)) {
            return 'existing';
        }

        if (!is_writable("{$folder}/thumbs") || !thumbs_generator_fits_in_memory("{$folder}/{$name}")) {
            return 'failed';
        }

        // a damaged image throws an error in GD or Imagick, the other images go on
        try {
            helper_thumb("{$folder}/{$name}", strtolower((string) $image['type']), $thumbnail, $width, $height);
        } catch (Throwable $error) {
            kleeja_log('[thumbs_generator] ' . $name . ': ' . $error->getMessage());
        }

        return file_exists($thumbnail) ? 'created' : 'failed';
    }

    /**
     * makes the missing thumbnails of the images that come after the image $after_id, in the order of their ids,
     * until $seconds have passed: a big site is done in many short steps, each one going on from the one before
     * @param  int   $after_id
     * @param  int   $seconds
     * @return array the counts of the step, the id of the last image it checked, and whether every image is checked
     */
    function thumbs_generator_step($after_id, $seconds)
    {
        global $SQL, $dbprefix;

        include_once PATH . 'includes/up_helpers/thumbs.php';

        [$width, $height] = thumbs_generator_dimensions();
        $stop_at = microtime(true) + $seconds;
        $step = [
            'last_id' => $after_id,
            'checked' => 0,
            'created' => 0,
            'existing' => 0,
            'missing' => 0,
            'failed' => 0,
            'done' => false,
        ];

        while (true) {
            $result = $SQL->build([
                'SELECT' => 'id, name, folder, type',
                'FROM' => "{$dbprefix}files",
                'WHERE' => 'id > :after_id AND type IN (:types)',
                'ORDER BY' => 'id ASC',
                'LIMIT' => THUMBS_GENERATOR_ROWS,
                'BIND' => ['after_id' => $step['last_id'], 'types' => THUMBS_GENERATOR_TYPES],
            ]);

            $images = [];

            while ($row = $SQL->fetch_array($result)) {
                $images[] = $row;
            }

            $SQL->freeresult($result);

            foreach ($images as $image) {
                // one image at least in every step, or a slow image would stop the generator for good
                if ($step['checked'] > 0 && microtime(true) >= $stop_at) {
                    return $step;
                }

                $step[thumbs_generator_image($image, $width, $height)]++;
                $step['checked']++;
                $step['last_id'] = (int) $image['id'];
            }

            if (count($images) < THUMBS_GENERATOR_ROWS) {
                $step['done'] = true;

                return $step;
            }
        }
    }

    /**
     * the words of the plugin in the language of the admin, a word that the translation misses is shown in English
     * @return array
     */
    function thumbs_generator_words()
    {
        global $config;
        static $words = [];

        $language = (string) ($config['language'] ?? 'en');

        if (!isset($words[$language])) {
            $translations = thumbs_generator_translations();
            $words[$language] = ($translations[$language] ?? []) + $translations['en'];
        }

        return $words[$language];
    }

    /**
     * the words of the guide on the help page, in the language of the admin,
     * a word that the translation misses is shown in English
     * @return array
     */
    function thumbs_generator_help_words()
    {
        global $config;

        $words = (array) require __DIR__ . '/language/help_en.php';
        $language = preg_replace('/[^a-z0-9_-]/i', '', (string) ($config['language'] ?? ''));
        $translation = __DIR__ . "/language/help_{$language}.php";

        if ($language !== '' && $language !== 'en' && file_exists($translation)) {
            // in its own line, Prettier drops the brackets of (require $translation) + $words
            $translated = require $translation;
            $words = (array) $translated + $words;
        }

        return $words;
    }

    /**
     * a section of the guide from its numbered words, like THUMBS_GENERATOR_HELP_TIP_1, THUMBS_GENERATOR_HELP_TIP_2 ..
     * its title, when it has its own, is THUMBS_GENERATOR_HELP_TIP_TITLE
     * @param  array  $words
     * @param  string $type  how Kleeja shows it: features, steps, tips, warnings or faq
     * @param  string $name  the name of its words, THUMBS_GENERATOR_HELP_{name}_1
     * @return array
     */
    function thumbs_generator_help_section($words, $type, $name)
    {
        $prefix = 'THUMBS_GENERATOR_HELP_' . $name;
        $section = ['type' => $type, 'title' => $words[$prefix . '_TITLE'] ?? '', 'items' => []];

        for ($n = 1; isset($words[$prefix . ($type === 'faq' ? '_Q_' : '_') . $n]); $n++) {
            $section['items'][] =
                $type === 'faq'
                    ? ['q' => $words[$prefix . '_Q_' . $n], 'a' => $words[$prefix . '_A_' . $n] ?? '']
                    : $words[$prefix . '_' . $n];
        }

        return $section;
    }

    /**
     * the words of the plugin, they are added to the language table when it is installed or updated.
     * Each one fits in the 255 letters of the column on MySQL
     * @return array
     */
    function thumbs_generator_translations()
    {
        return [
            'en' => [
                'R_THUMBS_GENERATOR' => 'Thumbnail Generator',
                'THUMBS_GENERATOR_INTRO' =>
                    'Create a thumbnail for every uploaded image that has none, such as images uploaded while the thumbs folder was missing. Images that already have a thumbnail are left unchanged.',
                'THUMBS_GENERATOR_DETAILS' => 'Details',
                'THUMBS_GENERATOR_IMAGES' => 'Uploaded images',
                'THUMBS_GENERATOR_SIZE' => 'Thumbnail size',
                'THUMBS_GENERATOR_LIBRARY' => 'Image library',
                'THUMBS_GENERATOR_CHANGE_SIZE' => 'Change the size',
                'THUMBS_GENERATOR_START' => 'Generate missing thumbnails',
                'THUMBS_GENERATOR_CONTINUE' => 'Continue',
                'THUMBS_GENERATOR_RUNNING' => 'Generating thumbnails. Keep this page open until it finishes.',
                'THUMBS_GENERATOR_PROGRESS' => 'Checked %1$s of %2$s images',
                'THUMBS_GENERATOR_CREATED' => 'Thumbnails created',
                'THUMBS_GENERATOR_EXISTING' => 'Already had a thumbnail',
                'THUMBS_GENERATOR_MISSING' => 'Original file not found',
                'THUMBS_GENERATOR_FAILED' => 'Could not be created',
                'THUMBS_GENERATOR_DONE' => 'Done. Every image has been checked.',
                'THUMBS_GENERATOR_PARTIAL' =>
                    'Some images were not checked yet, because the time a request may take ran out. Generate again to continue.',
                'THUMBS_GENERATOR_FAILED_NOTE' =>
                    'The images that could not get a thumbnail may be damaged or too large for the memory available to PHP, or their thumbs folder may not be writable.',
                'THUMBS_GENERATOR_MISSING_NOTE' =>
                    'The images whose original file was not found are listed in the database, but their files are not on this server.',
                'THUMBS_GENERATOR_ERROR' =>
                    'The generator stopped because the server did not answer as expected. Click Continue to resume where it stopped.',
                'THUMBS_GENERATOR_SESSION' =>
                    'Your control panel session has ended. Reload this page, sign in again, and start the generator again.',
                'THUMBS_GENERATOR_NO_IMAGES' => 'There are no uploaded images yet.',
                'THUMBS_GENERATOR_NO_LIBRARY' =>
                    'Thumbnails can\'t be created, because PHP has neither the GD nor the Imagick extension on this server. Ask your host to enable one of them.',
                'THUMBS_GENERATOR_NO_FOLDER' =>
                    'The folder %s doesn\'t exist or isn\'t writable, so the thumbnails of new uploads can\'t be saved there. Create it and make it writable.',
                'THUMBS_GENERATOR_LEAVE' => 'The generator is still running. Leave the page anyway?',
            ],
            'ar' => [
                'R_THUMBS_GENERATOR' => 'مولّد الصور المصغّرة',
                'THUMBS_GENERATOR_INTRO' =>
                    'أنشئ صورة مصغّرة لكل صورة مرفوعة ليست لها واحدة، مثل الصور التي رُفعت حين كان مجلد الصور المصغّرة مفقودًا. أما الصور التي لها صورة مصغّرة فتبقى كما هي.',
                'THUMBS_GENERATOR_DETAILS' => 'التفاصيل',
                'THUMBS_GENERATOR_IMAGES' => 'الصور المرفوعة',
                'THUMBS_GENERATOR_SIZE' => 'أبعاد الصورة المصغّرة',
                'THUMBS_GENERATOR_LIBRARY' => 'مكتبة معالجة الصور',
                'THUMBS_GENERATOR_CHANGE_SIZE' => 'تغيير الأبعاد',
                'THUMBS_GENERATOR_START' => 'إنشاء الصور المصغّرة الناقصة',
                'THUMBS_GENERATOR_CONTINUE' => 'متابعة',
                'THUMBS_GENERATOR_RUNNING' => 'جارٍ إنشاء الصور المصغّرة. أبقِ هذه الصفحة مفتوحة حتى ينتهي.',
                'THUMBS_GENERATOR_PROGRESS' => 'فُحصت %1$s من %2$s صورة',
                'THUMBS_GENERATOR_CREATED' => 'صور مصغّرة أُنشئت',
                'THUMBS_GENERATOR_EXISTING' => 'لها صورة مصغّرة من قبل',
                'THUMBS_GENERATOR_MISSING' => 'الملف الأصلي غير موجود',
                'THUMBS_GENERATOR_FAILED' => 'تعذّر إنشاؤها',
                'THUMBS_GENERATOR_DONE' => 'اكتمل العمل، وفُحصت كل الصور.',
                'THUMBS_GENERATOR_PARTIAL' =>
                    'بقيت صور لم تُفحص بعد، لأن المدة المسموح بها للطلب انتهت. اضغط زر الإنشاء مرة أخرى للمتابعة.',
                'THUMBS_GENERATOR_FAILED_NOTE' =>
                    'الصور التي تعذّر إنشاء صورها المصغّرة قد تكون تالفة أو أكبر من الذاكرة المتاحة لـ PHP، أو قد يكون مجلد الصور المصغّرة الخاص بها غير قابل للكتابة.',
                'THUMBS_GENERATOR_MISSING_NOTE' =>
                    'الصور التي لم يُعثر على ملفها الأصلي مسجلة في قاعدة البيانات، لكن ملفاتها ليست على هذا الخادم.',
                'THUMBS_GENERATOR_ERROR' =>
                    'توقف المولّد لأن الخادم لم يرد كما يجب. اضغط «متابعة» ليستأنف من حيث توقف.',
                'THUMBS_GENERATOR_SESSION' =>
                    'انتهت جلستك في لوحة التحكم. أعد تحميل هذه الصفحة، وسجّل الدخول من جديد، ثم شغّل المولّد مرة أخرى.',
                'THUMBS_GENERATOR_NO_IMAGES' => 'لا توجد صور مرفوعة بعد.',
                'THUMBS_GENERATOR_NO_LIBRARY' =>
                    'لا يمكن إنشاء الصور المصغّرة، لأن PHP على هذا الخادم ليس فيه الامتداد GD ولا Imagick. اطلب من مزوّد الاستضافة تفعيل أحدهما.',
                'THUMBS_GENERATOR_NO_FOLDER' =>
                    'المجلد %s غير موجود أو غير قابل للكتابة، فلا يمكن حفظ الصور المصغّرة للملفات الجديدة فيه. أنشئه واجعله قابلًا للكتابة.',
                'THUMBS_GENERATOR_LEAVE' => 'ما زال المولّد يعمل. هل تريد مغادرة الصفحة على أي حال؟',
            ],
        ];
    }
}
