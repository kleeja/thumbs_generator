<?php
//
// thumbs_generator, its guide on the help page of the control panel
// Arabic
//
// The guide is built by the admin_help_guides hook in init.php, the texts here can have HTML.
// The keys are the ones of help_en.php, a key that is missing here is shown in English.
//  - names in <strong> are written as the control panel shows them, the words of Kleeja are in lang/ar/,
//    the words of the plugin are in thumbs_generator_translations() of init.php
//  - a <code> that begins or ends with a symbol has dir="ltr", or the symbol moves to the other side
//

return [
    'THUMBS_GENERATOR_HELP_TITLE' => 'مولّد الصور المصغّرة',
    'THUMBS_GENERATOR_HELP_INTRO' =>
        'ينشئ كليجا صورة مصغّرة لكل صورة عند رفعها، ويُنشئ مولّد الصور المصغّرة ما نقص منها: للصور التي رُفعت حين كان مجلد الصور المصغّرة مفقودًا، أو التي نُقلت من خادم آخر دون صورها المصغّرة.',

    //
    // what the plugin does
    //
    'THUMBS_GENERATOR_HELP_FEATURE_1' =>
        'يفحص كل الصور المرفوعة بصيغ JPG وPNG وGIF وBMP، ولا ينشئ صورة مصغّرة إلا للصور التي ليست لها واحدة. ولا يغيّر الصور المصغّرة الموجودة أبدًا.',
    'THUMBS_GENERATOR_HELP_FEATURE_2' =>
        'يستخدم أبعاد الصور المصغّرة المحددة في <strong>اعدادات التحميل</strong>، وهي الأبعاد نفسها التي يعطيها كليجا للملفات الجديدة.',
    'THUMBS_GENERATOR_HELP_FEATURE_3' =>
        'يعمل على خطوات قصيرة مع شريط تقدّم، فيُكمل عمله في المواقع الكبيرة دون أن يبلغ الحد الزمني لـ PHP.',
    'THUMBS_GENERATOR_HELP_FEATURE_4' =>
        'يعرض عدد الصور المصغّرة التي أنشأها، وعدد الصور التي تخطّاها لأن ملفها مفقود أو لأنه تعذّر إنشاء صورتها المصغّرة.',

    //
    // how to use it
    //
    'THUMBS_GENERATOR_HELP_STEP_1' => 'افتح <strong>مولّد الصور المصغّرة</strong> من قائمة لوحة التحكم.',
    'THUMBS_GENERATOR_HELP_STEP_2' =>
        'تحقق من <strong>أبعاد الصورة المصغّرة</strong>. ولتغييرها، عدّل أولًا <strong>أبعاد مصغرات الصور (عرض x إرتفاع)</strong> من <strong>إعدادات ← اعدادات التحميل</strong>.',
    'THUMBS_GENERATOR_HELP_STEP_3' =>
        'اضغط <strong>إنشاء الصور المصغّرة الناقصة</strong>، وأبقِ الصفحة مفتوحة حتى يمتلئ شريط التقدّم.',
    'THUMBS_GENERATOR_HELP_STEP_4' => 'افتح <strong>إدارة الصور</strong> لترى الصور مع صورها المصغّرة.',

    //
    // common questions
    //
    'THUMBS_GENERATOR_HELP_FAQ_Q_1' => 'هل يغيّر المولّد أبعاد الصور المصغّرة الموجودة؟',
    'THUMBS_GENERATOR_HELP_FAQ_A_1' =>
        'لا. تُتخطّى الصورة التي لها صورة مصغّرة، حتى لو اختلفت أبعادها عن الإعداد الحالي. ولتحصل صورة على صورة مصغّرة بالأبعاد الجديدة، احذف صورتها المصغّرة القديمة من مجلد <code>thumbs</code> داخل مجلد الرفع (<code>uploads/thumbs</code> افتراضيًا)، ثم شغّل المولّد مرة أخرى.',
    'THUMBS_GENERATOR_HELP_FAQ_Q_2' => 'لماذا تعذّر إنشاء بعض الصور المصغّرة؟',
    'THUMBS_GENERATOR_HELP_FAQ_A_2' =>
        'قد تكون الصورة تالفة، أو أكبر من الذاكرة المسموح بها لـ PHP، فارفع قيمة <code>memory_limit</code> من إعدادات PHP في استضافتك. وقد يكون مجلد <code>thumbs</code> غير قابل للكتابة.',
    'THUMBS_GENERATOR_HELP_FAQ_Q_3' => 'ماذا يعني «الملف الأصلي غير موجود»؟',
    'THUMBS_GENERATOR_HELP_FAQ_A_3' =>
        'الصورة مسجلة في قاعدة البيانات، لكن ملفها ليس على هذا الخادم: إما أنه حُذف من القرص، أو أنه محفوظ في مكان آخر مثل خادم FTP. ولا يعمل المولّد إلا على الملفات الموجودة على هذا الخادم.',
    'THUMBS_GENERATOR_HELP_FAQ_Q_4' => 'ماذا يحدث إذا أغلقت الصفحة أثناء عمل المولّد؟',
    'THUMBS_GENERATOR_HELP_FAQ_A_4' =>
        'يتوقف بعد الخطوة الجارية. وتبقى الصور المصغّرة التي أُنشئت حتى تلك اللحظة، ويتخطّاها التشغيل التالي. وإذا توقف بسبب خطأ، فاضغط <strong>متابعة</strong> ليستأنف من حيث توقف.',

    //
    // beside the guide
    //
    'THUMBS_GENERATOR_HELP_TIP_1' =>
        'شغّل المولّد بعد نقل كليجا إلى خادم جديد، أو بعد استعادة مجلد الرفع من نسخة احتياطية لا تضم الصور المصغّرة.',
    'THUMBS_GENERATOR_HELP_TIP_2' =>
        'في المواقع المزدحمة، شغّله في وقت قليل الزيارات، لأن إنشاء الصور المصغّرة يستهلك وقت المعالج.',

    'THUMBS_GENERATOR_HELP_WARNING_1' =>
        'يجب أن يكون مجلد <code>thumbs</code> (<code>uploads/thumbs</code> افتراضيًا) موجودًا وقابلًا للكتابة، وإلا فلن تُحفظ أي صورة مصغّرة.',

    //
    // in the guide of Image control, the page that shows the thumbnails
    //
    'THUMBS_GENERATOR_HELP_NOTE' =>
        'صور بلا صور مصغّرة؟ تُنشئ إضافة <strong>مولّد الصور المصغّرة</strong> ما نقص منها. <a href="#help-thumbs_generator">اقرأ دليلها</a>.',
];
