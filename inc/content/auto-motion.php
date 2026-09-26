<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * This page is rendered by page-auto-motion.php (a dedicated
 * template, same pattern as page-lead-form.php / page-contact.php), not
 * by this file's post_content — the embedded Automation Audit form
 * needs a fresh nonce and a live $_GET check on every request, which a
 * statically stored HTML string can't provide. This content is only a
 * fallback/description for contexts that don't use the custom template
 * (search results, RSS, etc).
 */
return <<<'HTML'
<p>Lead সংগ্রহ, Instant Reply, Follow-up, CRM, Invoice ও Business Report — একটি সমন্বিত Business Automation System-এর মাধ্যমে পরিচালনা করুন। বিস্তারিত জানতে ফ্রি Business Automation Audit করুন।</p>
HTML;
