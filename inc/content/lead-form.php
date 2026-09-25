<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * This page is rendered by page-lead-form.php (a dedicated template that
 * calls womensfight_render_customer_form() directly), not by this file's
 * post_content — the form needs a fresh nonce and a live $_GET check on
 * every request, which a statically stored HTML string can't provide.
 * This content is only a fallback/description for contexts that don't
 * use the custom template (search results, RSS, etc).
 */
return <<<'HTML'
<p>এই পেজে একটি সহজ Customer Information Form আছে — নাম, মোবাইল, WhatsApp, ইমেইল, ব্যবসার তথ্য ও প্রয়োজন জানাতে ফর্মটি পূরণ করুন।</p>
HTML;
