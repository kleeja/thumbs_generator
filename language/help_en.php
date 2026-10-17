<?php
//
// thumbs_generator, its guide on the help page of the control panel
// English
//
// The guide is built by the admin_help_guides hook in init.php, the texts here can have HTML.
//  - a list is numbered: THUMBS_GENERATOR_HELP_TIP_1, THUMBS_GENERATOR_HELP_TIP_2 ... it ends at the first missing number,
//    so an item can be added or removed without touching the code
//  - THUMBS_GENERATOR_HELP_{LIST}_TITLE is the title of the list, a list without it takes the title that Kleeja gives its type
//  - questions and answers are THUMBS_GENERATOR_HELP_FAQ_Q_1 and THUMBS_GENERATOR_HELP_FAQ_A_1
//  - names in <strong> are written as the control panel shows them, the words of Kleeja are in lang/en/,
//    the words of the plugin are in thumbs_generator_translations() of init.php
//

return [
    'THUMBS_GENERATOR_HELP_TITLE' => 'Thumbnail Generator',
    'THUMBS_GENERATOR_HELP_INTRO' =>
        'Kleeja makes a thumbnail when an image is uploaded. The Thumbnail Generator creates the ones that are missing: for images uploaded while the thumbs folder was missing, or moved from another server without their thumbnails.',

    //
    // what the plugin does
    //
    'THUMBS_GENERATOR_HELP_FEATURE_1' =>
        'Checks every uploaded JPG, PNG, GIF and BMP image, and creates a thumbnail only for the images that have none. Existing thumbnails are never changed.',
    'THUMBS_GENERATOR_HELP_FEATURE_2' =>
        'Uses the thumbnail size of <strong>Upload settings</strong>, the same size that Kleeja gives new uploads.',
    'THUMBS_GENERATOR_HELP_FEATURE_3' =>
        'Works in short steps with a progress bar, so it finishes on large sites without reaching the time limit of PHP.',
    'THUMBS_GENERATOR_HELP_FEATURE_4' =>
        'Reports how many thumbnails it created, and how many images it skipped because their file is missing or their thumbnail could not be created.',

    //
    // how to use it
    //
    'THUMBS_GENERATOR_HELP_STEP_1' => 'Open <strong>Thumbnail Generator</strong> in the menu of the control panel.',
    'THUMBS_GENERATOR_HELP_STEP_2' =>
        'Check the <strong>Thumbnail size</strong>. To change it, first set <strong>thumbnails Dimensions (Width x Height)</strong> in <strong>Settings → Upload settings</strong>.',
    'THUMBS_GENERATOR_HELP_STEP_3' =>
        'Click <strong>Generate missing thumbnails</strong>, and keep the page open until the progress bar is full.',
    'THUMBS_GENERATOR_HELP_STEP_4' =>
        'Open <strong>Image control only</strong> to see the images with their thumbnails.',

    //
    // common questions
    //
    'THUMBS_GENERATOR_HELP_FAQ_Q_1' => 'Does the generator resize the thumbnails that already exist?',
    'THUMBS_GENERATOR_HELP_FAQ_A_1' =>
        'No. An image that has a thumbnail is skipped, even when its size differs from the current setting. To give an image a thumbnail in the new size, delete its old one from the <code>thumbs</code> folder inside the upload folder (<code>uploads/thumbs</code> by default), then run the generator again.',
    'THUMBS_GENERATOR_HELP_FAQ_Q_2' => 'Why could some thumbnails not be created?',
    'THUMBS_GENERATOR_HELP_FAQ_A_2' =>
        'The image may be damaged, or too large for the memory that PHP may use: raise <code>memory_limit</code> in the PHP settings of your host. The <code>thumbs</code> folder may also not be writable.',
    'THUMBS_GENERATOR_HELP_FAQ_Q_3' => 'What does “Original file not found” mean?',
    'THUMBS_GENERATOR_HELP_FAQ_A_3' =>
        'The image is listed in the database, but its file is not on this server: it was deleted from the disk, or it is stored elsewhere, such as on an FTP server. The generator only works with files on this server.',
    'THUMBS_GENERATOR_HELP_FAQ_Q_4' => 'What happens if I close the page while the generator runs?',
    'THUMBS_GENERATOR_HELP_FAQ_A_4' =>
        'It stops after the current step. The thumbnails created so far are kept, and the next run skips them. If it stops because of an error, click <strong>Continue</strong> to resume where it stopped.',

    //
    // beside the guide
    //
    'THUMBS_GENERATOR_HELP_TIP_1' =>
        'Run the generator after moving Kleeja to a new server, or after restoring the upload folder from a backup that has no thumbnails.',
    'THUMBS_GENERATOR_HELP_TIP_2' =>
        'On a busy site, run it when traffic is low: creating thumbnails takes processor time.',

    'THUMBS_GENERATOR_HELP_WARNING_1' =>
        'The <code>thumbs</code> folder (<code>uploads/thumbs</code> by default) must exist and be writable, or no thumbnail can be saved.',

    //
    // in the guide of Image control, the page that shows the thumbnails
    //
    'THUMBS_GENERATOR_HELP_NOTE' =>
        'Images without a thumbnail? The <strong>Thumbnail Generator</strong> plugin creates the missing ones. <a href="#help-thumbs_generator">Read its guide</a>.',
];
