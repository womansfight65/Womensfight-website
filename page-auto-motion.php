<?php
/**
 * Dedicated template for the "Business Automation Service" page
 * (slug: auto-motion) — SAMPLE page, pending approval.
 *
 * Same pattern as page-lead-form.php / page-contact.php: rendered
 * directly via PHP instead of through the_content(), because the
 * embedded Automation Audit form (via womensfight_render_customer_form())
 * needs a fresh nonce and a live ?wf_submitted check on every request,
 * which Elementor's cached _elementor_data can't provide.
 *
 * The "Sample Automation Dashboard" section below is a frontend-only
 * demonstration using hardcoded sample data — no backend, no database,
 * no real automation is wired up behind it. Only the Automation Audit
 * form at the bottom submits real data, via the same secure customer-form
 * backend already used by the Lead Form page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main>

<div class="hero wrap">
  <div class="hero-glow-1"></div><div class="hero-glow-2"></div>
  <div class="hero-grid">
    <div>
      <span class="eyebrow">Business Automation Service</span>
      <h1>Lead আসার পর আর কোনো Customer হারাবেন না</h1>
      <p class="lead">আপনার Facebook, Website ও Landing Page থেকে আসা প্রতিটি Lead সংগ্রহ, Instant Reply, Follow-up, Customer Management, Invoice এবং Business Report&mdash;সবকিছু একটি Smart Automation System-এর মাধ্যমে পরিচালনা করুন।</p>
      <div class="hero-ctas">
        <a class="btn btn-primary" href="#wf-audit-form">ফ্রি Business Automation Audit করুন</a>
        <a class="btn btn-ghost" href="#wf-sample-dashboard">Sample System দেখুন</a>
      </div>
    </div>
    <div class="hero-visual">
      <div class="aut-mock">
        <div class="aut-mock-bar"><span></span><span></span><span></span></div>
        <div class="aut-mock-body">
          <div class="aut-mock-row"><div class="ico sm"><svg><use href="#i-form"/></svg></div><div><b>Lead CRM</b><span>নতুন Lead সংগ্রহ হচ্ছে</span></div></div>
          <div class="aut-mock-row"><div class="ico sm"><svg><use href="#i-chat"/></svg></div><div><b>WhatsApp</b><span>Instant Confirmation পাঠানো হয়েছে</span></div></div>
          <div class="aut-mock-row"><div class="ico sm"><svg><use href="#i-route"/></svg></div><div><b>Follow-up</b><span>Reminder সেট করা আছে</span></div></div>
          <div class="aut-mock-row"><div class="ico sm"><svg><use href="#i-chart"/></svg></div><div><b>Report</b><span>Daily Summary তৈরি হচ্ছে</span></div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">সমস্যা</span>
    <h2>Manual কাজের কারণে আপনার Business কি পিছিয়ে যাচ্ছে?</h2>
  </div>
  <div class="feat-grid">
    <div class="feat"><div class="ico sm"><svg><use href="#i-chat"/></svg></div><h3>দেরি হওয়া Reply</h3><p>Lead আসলেও সময়মতো Reply দেওয়া হয় না, ফলে Customer অন্য জায়গায় চলে যায়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-split"/></svg></div><h3>বিক্ষিপ্ত তথ্য</h3><p>Customer-এর তথ্য আলাদা আলাদা জায়গায় থাকে — Excel, খাতা, মেসেঞ্জার সব মিলিয়ে খুঁজে পাওয়া কঠিন।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-route"/></svg></div><h3>Follow-up বাদ পড়া</h3><p>নিয়মিত Follow-up করা হয় না, ফলে আগ্রহী Customer-ও হারিয়ে যায়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-users"/></svg></div><h3>Team Track করা যায় না</h3><p>Sales Team কোন Lead নিয়ে কাজ করছে, কতদূর এগিয়েছে তা বোঝা যায় না।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>Invoice জটিলতা</h3><p>Invoice ও Due হিসাব ম্যানুয়ালি রাখতে গিয়ে ভুল ও সময় নষ্ট হয়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-chart"/></svg></div><h3>স্পষ্ট Report নেই</h3><p>Business Owner সঠিক সময়ে সঠিক Report না পাওয়ায় সিদ্ধান্ত নিতে দেরি হয়।</p></div>
  </div>
</section>

<section class="tight alt"><div class="wrap">
  <div class="section-head">
    <span class="eyebrow">কর্মপদ্ধতি</span>
    <h2>Lead থেকে Sale&mdash;পুরো Journey একটি System-এ</h2>
  </div>
  <div class="timeline">
    <div class="tl-step"><div class="tl-num">১</div><div class="tl-body"><h3>Facebook Ad / Website Form</h3><p>Lead প্রথম যেখান থেকে আসে — Facebook Ad, Landing Page বা Website Form।</p></div></div>
    <div class="tl-step"><div class="tl-num">২</div><div class="tl-body"><h3>Lead CRM</h3><p>প্রতিটি Lead স্বয়ংক্রিয়ভাবে একটি সংগঠিত CRM-এ জমা হয়।</p></div></div>
    <div class="tl-step"><div class="tl-num">৩</div><div class="tl-body"><h3>Instant Confirmation</h3><p>Customer সাথে সাথে একটি নিশ্চিতকরণ বার্তা পান, যাতে অপেক্ষার অনুভূতি না হয়।</p></div></div>
    <div class="tl-step"><div class="tl-num">৪</div><div class="tl-body"><h3>Salesperson Assignment</h3><p>Lead নির্দিষ্ট Sales Staff-এর কাছে বণ্টন হয়, দায়িত্ব স্পষ্ট থাকে।</p></div></div>
    <div class="tl-step"><div class="tl-num">৫</div><div class="tl-body"><h3>Follow-up Reminder</h3><p>নির্দিষ্ট সময়ে Follow-up করার Reminder Sales Staff-কে জানিয়ে দেওয়া হয়।</p></div></div>
    <div class="tl-step"><div class="tl-num">৬</div><div class="tl-body"><h3>Customer / Sale</h3><p>আলোচনা সফল হলে Lead থেকে Customer-এ রূপান্তর রেকর্ড হয়।</p></div></div>
    <div class="tl-step"><div class="tl-num">৭</div><div class="tl-body"><h3>Invoice</h3><p>বিক্রয় নিশ্চিত হলে Invoice তৈরি হয় ও Due ট্র্যাক করা যায়।</p></div></div>
    <div class="tl-step"><div class="tl-num">৮</div><div class="tl-body"><h3>Daily &amp; Monthly Report</h3><p>প্রতিদিন ও প্রতি মাসে Business-এর সার্বিক চিত্র একটি Report-এ পাওয়া যায়।</p></div></div>
  </div>
</div></section>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">ফিচার</span>
    <h2>Automation Features</h2>
  </div>
  <div class="feat-grid">
    <div class="feat"><div class="ico sm"><svg><use href="#i-form"/></svg></div><h3>Lead Collection Automation</h3><p>বিভিন্ন সোর্স থেকে আসা Lead স্বয়ংক্রিয়ভাবে এক জায়গায় জমা হয়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-chat"/></svg></div><h3>Instant WhatsApp Response</h3><p>Lead আসার সাথে সাথে একটি স্বয়ংক্রিয় WhatsApp বার্তা পাঠানো হয়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-layers"/></svg></div><h3>Smart CRM &amp; Lead Management</h3><p>প্রতিটি Lead-এর তথ্য, স্ট্যাটাস ও ইতিহাস সংগঠিতভাবে সংরক্ষিত থাকে।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-route"/></svg></div><h3>Automatic Follow-up Reminder</h3><p>নির্দিষ্ট সময় পর Follow-up করার Reminder স্বয়ংক্রিয়ভাবে আসে।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-users"/></svg></div><h3>Sales Team Assignment</h3><p>Lead সঠিক Sales Staff-এর কাছে বণ্টন হয়, কাজের ভাগ স্পষ্ট থাকে।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-mobile"/></svg></div><h3>Appointment Booking</h3><p>Customer সহজে অ্যাপয়েন্টমেন্ট বুক করতে পারেন, সময় নষ্ট হয় না।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>Invoice &amp; Due Reminder</h3><p>Invoice তৈরি ও বকেয়া টাকার Reminder স্বয়ংক্রিয়ভাবে পাঠানো যায়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-mask"/></svg></div><h3>Customer Support Automation</h3><p>সাধারণ প্রশ্নের উত্তর দ্রুত ও ধারাবাহিকভাবে দেওয়া যায়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-heart"/></svg></div><h3>Review &amp; Repeat Sale</h3><p>পুরনো Customer-দের কাছ থেকে Review ও Repeat Sale আনার সিস্টেম।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-chart"/></svg></div><h3>Daily and Monthly Business Report</h3><p>প্রতিদিন ও মাসে Business-এর অবস্থা এক নজরে দেখার Report।</p></div>
  </div>
</section>

<section class="tight alt" id="wf-sample-dashboard"><div class="wrap">
  <div class="section-head">
    <span class="eyebrow">Sample Automation Dashboard</span>
    <h2>Dashboard-এ যেভাবে সবকিছু দেখা যাবে</h2>
    <p>নিচেরটি শুধুমাত্র একটি Frontend Demo — নমুনা তথ্য দিয়ে দেখানো হয়েছে, এখনো কোনো Live Data বা Backend যুক্ত নয়।</p>
  </div>

  <div class="aut-dash-stats">
    <div><b>২৪</b><span>আজকের Lead</span></div>
    <div><b>৮</b><span>নতুন Lead</span></div>
    <div><b>৬</b><span>Follow-up বাকি</span></div>
    <div><b>৪</b><span>Hot Lead</span></div>
    <div><b>৩</b><span>Customer হয়েছে</span></div>
    <div><b>১২.৫%</b><span>Conversion Rate</span></div>
  </div>

  <div class="wf-demo-filters" id="aut-filters">
    <button type="button" class="wf-demo-filter active" data-status="All">সব</button>
    <button type="button" class="wf-demo-filter" data-status="New Lead">New Lead</button>
    <button type="button" class="wf-demo-filter" data-status="Contacted">Contacted</button>
    <button type="button" class="wf-demo-filter" data-status="Interested">Interested</button>
    <button type="button" class="wf-demo-filter" data-status="Follow-up">Follow-up</button>
    <button type="button" class="wf-demo-filter" data-status="Hot Lead">Hot Lead</button>
    <button type="button" class="wf-demo-filter" data-status="Customer">Customer</button>
  </div>

  <div class="aut-table-wrap">
    <table class="aut-table">
      <thead>
        <tr><th>Customer</th><th>Source</th><th>Service</th><th>Status</th><th>Assigned To</th><th>Next Follow-up</th></tr>
      </thead>
      <tbody id="aut-table-body">
        <tr data-status="New Lead"><td>রাফি হাসান</td><td>Facebook Ad</td><td>Digital Marketing</td><td><span class="aut-badge new">New Lead</span></td><td>সুমাইয়া</td><td>আজ</td></tr>
        <tr data-status="Contacted"><td>নাসরিন আক্তার</td><td>Website Form</td><td>Website Development</td><td><span class="aut-badge contacted">Contacted</span></td><td>ইমরান</td><td>আগামীকাল</td></tr>
        <tr data-status="Interested"><td>তানভীর আহমেদ</td><td>Landing Page</td><td>Business Automation</td><td><span class="aut-badge interested">Interested</span></td><td>সুমাইয়া</td><td>২ দিন পর</td></tr>
        <tr data-status="Follow-up"><td>মিতু সরকার</td><td>Facebook Ad</td><td>Video Production</td><td><span class="aut-badge followup">Follow-up</span></td><td>ইমরান</td><td>আজ</td></tr>
        <tr data-status="Hot Lead"><td>শাকিল মিয়া</td><td>WhatsApp</td><td>Digital Marketing</td><td><span class="aut-badge hot">Hot Lead</span></td><td>সুমাইয়া</td><td>আজ</td></tr>
        <tr data-status="Customer"><td>ফারজানা ইসলাম</td><td>Website Form</td><td>Business Automation</td><td><span class="aut-badge customer">Customer</span></td><td>ইমরান</td><td>&mdash;</td></tr>
        <tr data-status="New Lead"><td>ওবায়দুল করিম</td><td>Landing Page</td><td>Website Development</td><td><span class="aut-badge new">New Lead</span></td><td>সুমাইয়া</td><td>আজ</td></tr>
        <tr data-status="Hot Lead"><td>রুমানা পারভীন</td><td>Facebook Ad</td><td>Business Automation</td><td><span class="aut-badge hot">Hot Lead</span></td><td>ইমরান</td><td>আজ</td></tr>
      </tbody>
    </table>
  </div>
  <p class="note">* এই টেবিলের নাম ও তথ্য শুধুমাত্র নমুনা — বাস্তব কোনো Customer-এর তথ্য নয়।</p>
</div></section>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">সুবিধা</span>
    <h2>এই System ব্যবহার করলে আপনার কী লাভ হবে?</h2>
  </div>
  <div class="benefits">
    <div class="benefit"><div class="ico sm"><svg><use href="#i-shield"/></svg></div><h3>কোনো Lead হারাবে না</h3><p>প্রতিটি Lead সংগ্রহ ও ট্র্যাক হয়।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-bolt"/></svg></div><h3>দ্রুত উত্তর</h3><p>Customer দ্রুত উত্তর পাবে।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-route"/></svg></div><h3>নিয়মিত Follow-up</h3><p>কোনো Follow-up আর বাদ পড়বে না।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-target"/></svg></div><h3>বেশি Conversion</h3><p>Sales Conversion বাড়বে।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-check"/></svg></div><h3>কম ভুল</h3><p>Manual কাজ ও ভুল কমবে।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-users"/></svg></div><h3>Team Tracking</h3><p>Team-এর কাজ Track করা যাবে।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>সহজ Invoice</h3><p>Invoice এবং Due নিয়ন্ত্রণ সহজ হবে।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-chart"/></svg></div><h3>এক Dashboard</h3><p>সব তথ্য একটি Dashboard-এ পাওয়া যাবে।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-heart"/></svg></div><h3>Repeat Sale</h3><p>পুরোনো Customer থেকে Repeat Sale আসবে।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-mobile"/></svg></div><h3>দূর থেকে নিয়ন্ত্রণ</h3><p>দূরে থেকেও Business নিয়ন্ত্রণ করা যাবে।</p></div>
  </div>
</section>

<section class="tight alt"><div class="wrap">
  <div class="section-head">
    <span class="eyebrow">উপযুক্ত কাদের জন্য</span>
    <h2>Who This Service Is For</h2>
  </div>
  <div class="aut-sector-grid">
    <div class="feat"><div class="ico sm"><svg><use href="#i-layers"/></svg></div><h3>Training Center</h3></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-cart"/></svg></div><h3>Facebook &amp; E-commerce Business</h3></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-heart"/></svg></div><h3>Clinic &amp; Healthcare Service</h3></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-mask"/></svg></div><h3>Beauty Parlour</h3></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-pin"/></svg></div><h3>Real Estate</h3></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-route"/></svg></div><h3>Travel Agency</h3></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-brain"/></svg></div><h3>Digital Agency</h3></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-gear"/></svg></div><h3>Local Service Business</h3></div>
  </div>
</div></section>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">প্যাকেজ</span>
    <h2>Service Packages</h2>
    <p>নির্দিষ্ট মূল্য এখানে দেখানো হয়নি — প্রতিটি Business-এর কাজের ধরন অনুযায়ী Automation Plan ও মূল্য নির্ধারণ করা হয়।</p>
  </div>
  <div class="aut-pkg-grid">
    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-bolt"/></svg></div>
      <h3>Starter Automation</h3>
      <div class="price">নমুনা প্যাকেজ <span>কাস্টম কোটের জন্য যোগাযোগ করুন</span></div>
      <ul>
        <li><svg><use href="#i-check"/></svg>Website Lead Form</li>
        <li><svg><use href="#i-check"/></svg>Lead Sheet / Basic CRM</li>
        <li><svg><use href="#i-check"/></svg>Instant Team Notification</li>
        <li><svg><use href="#i-check"/></svg>Basic Follow-up System</li>
        <li><svg><use href="#i-check"/></svg>Weekly Report</li>
      </ul>
      <a class="btn btn-ghost btn-block" href="#wf-audit-form">Custom Quote নিন</a>
    </div>
    <div class="pkg feat-pkg">
      <span class="pkg-badge">সবচেয়ে জনপ্রিয়</span>
      <div class="ico sm"><svg><use href="#i-target"/></svg></div>
      <h3>Growth Automation</h3>
      <div class="price">নমুনা প্যাকেজ <span>কাস্টম কোটের জন্য যোগাযোগ করুন</span></div>
      <ul>
        <li><svg><use href="#i-check"/></svg>Everything in Starter</li>
        <li><svg><use href="#i-check"/></svg>Advanced CRM</li>
        <li><svg><use href="#i-check"/></svg>WhatsApp Response</li>
        <li><svg><use href="#i-check"/></svg>Lead Assignment</li>
        <li><svg><use href="#i-check"/></svg>Follow-up Reminder</li>
        <li><svg><use href="#i-check"/></svg>Sales Dashboard</li>
        <li><svg><use href="#i-check"/></svg>Monthly Report</li>
      </ul>
      <a class="btn btn-primary btn-block" href="#wf-audit-form">Custom Quote নিন</a>
    </div>
    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-brain"/></svg></div>
      <h3>Complete Automation</h3>
      <div class="price">নমুনা প্যাকেজ <span>কাস্টম কোটের জন্য যোগাযোগ করুন</span></div>
      <ul>
        <li><svg><use href="#i-check"/></svg>Everything in Growth</li>
        <li><svg><use href="#i-check"/></svg>AI Customer Support</li>
        <li><svg><use href="#i-check"/></svg>Appointment System</li>
        <li><svg><use href="#i-check"/></svg>Invoice &amp; Due Reminder</li>
        <li><svg><use href="#i-check"/></svg>Payment Workflow</li>
        <li><svg><use href="#i-check"/></svg>Review Automation</li>
        <li><svg><use href="#i-check"/></svg>Custom Dashboard</li>
        <li><svg><use href="#i-check"/></svg>Priority Support</li>
      </ul>
      <a class="btn btn-ghost btn-block" href="#wf-audit-form">Custom Quote নিন</a>
    </div>
  </div>
  <p class="note">প্রতিটি Business-এর কাজের ধরন আলাদা। তাই আপনার প্রয়োজন অনুযায়ী Automation Plan ও মূল্য নির্ধারণ করা হবে।</p>
</section>

<section class="tight alt" id="wf-audit-form"><div class="wrap">
  <div class="section-head center">
    <span class="eyebrow">ফ্রি Automation Audit</span>
    <h2>আপনার Business-এর জন্য কোন Automation প্রয়োজন জানুন</h2>
  </div>
  <?php echo womensfight_render_customer_form(); ?>
  <script>
  (function(){
    var sel = document.getElementById('wf_service');
    if (sel && !sel.value) {
      sel.value = 'Business Automation';
      if (typeof wfToggleServiceFields === 'function') { wfToggleServiceFields('Business Automation'); }
    }
  })();
  </script>
</div></section>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">সাধারণ প্রশ্ন</span>
    <h2>যা জানতে চান</h2>
  </div>
  <div class="faq">
    <details open><summary>Business Automation কী?</summary><p>Lead সংগ্রহ থেকে শুরু করে Reply, Follow-up, Sales Tracking, Invoice ও Report পর্যন্ত পুরো প্রক্রিয়া একটি সংগঠিত System দিয়ে পরিচালনা করাকে Business Automation বলা হয়।</p></details>
    <details><summary>আমার Website না থাকলে কি এই Service নিতে পারব?</summary><p>হ্যাঁ। Facebook Page বা WhatsApp থেকে আসা Lead দিয়েও Automation শুরু করা যায় — Website না থাকলেও সমস্যা নেই।</p></details>
    <details><summary>WhatsApp কি Automation-এর সঙ্গে যুক্ত করা যাবে?</summary><p>হ্যাঁ, WhatsApp-এর মাধ্যমে Instant Response ও Follow-up যুক্ত করা যায়।</p></details>
    <details><summary>Setup করতে কতদিন লাগবে?</summary><p>আপনার Business ও প্রয়োজনীয় Features-এর উপর নির্ভর করে সময় নির্ধারিত হয় — Audit-এর পর সঠিক সময়সীমা জানানো হবে।</p></details>
    <details><summary>আমার Team কি Dashboard ব্যবহার করতে পারবে?</summary><p>হ্যাঁ, Team-এর সদস্যরা তাদের নিজস্ব Access দিয়ে Dashboard ব্যবহার করতে পারবেন।</p></details>
    <details><summary>মাসিক Support পাওয়া যাবে?</summary><p>হ্যাঁ, প্যাকেজ অনুযায়ী নিয়মিত Support ও রক্ষণাবেক্ষণ দেওয়া হয়।</p></details>
    <details><summary>আমার Business অনুযায়ী System customize করা যাবে?</summary><p>হ্যাঁ, প্রতিটি Business-এর কাজের ধরন অনুযায়ী System সাজিয়ে দেওয়া হয়।</p></details>
    <details><summary>Client-এর Data কীভাবে নিরাপদ থাকবে?</summary><p>প্রতিটি Client-এর তথ্য সুরক্ষিতভাবে সংরক্ষণ করা হয় এবং অনুমতি ছাড়া তৃতীয় পক্ষের সাথে শেয়ার করা হয় না।</p></details>
  </div>
</section>

<section class="tight wrap"><div class="cta-band">
  <div><h2>Manual কাজ কমিয়ে Business Growth-এ মনোযোগ দিন</h2><p>আপনার বর্তমান Business Process আমরা বিশ্লেষণ করে কোথায় Automation প্রয়োজন, তার একটি বাস্তব পরিকল্পনা তৈরি করব।</p></div>
  <div style="display:flex; gap:14px; flex-wrap:wrap;">
    <a class="btn btn-primary" href="#wf-audit-form">ফ্রি Business Automation Audit করুন</a>
    <a class="btn btn-ghost" href="https://wa.me/8801748133740?text=<?php echo rawurlencode( 'আসসালামু আলাইকুম, আমি Business Automation Service সম্পর্কে জানতে চাই।' ); ?>" target="_blank" rel="noopener">WhatsApp-এ কথা বলুন</a>
  </div>
</div></section>

</main>
<script>
document.querySelectorAll('#aut-filters .wf-demo-filter').forEach(function(btn){
	btn.addEventListener('click', function(){
		document.querySelectorAll('#aut-filters .wf-demo-filter').forEach(function(b){ b.classList.remove('active'); });
		this.classList.add('active');
		var status = this.getAttribute('data-status');
		document.querySelectorAll('#aut-table-body tr').forEach(function(row){
			var show = (status === 'All' || row.getAttribute('data-status') === status);
			row.style.display = show ? '' : 'none';
		});
	});
});
</script>
<?php
get_footer();
