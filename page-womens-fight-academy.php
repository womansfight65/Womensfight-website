<?php
/**
 * Template for the "Women's Fight Academy" page (slug: womens-fight-academy).
 *
 * This page and its lead system are kept isolated from the rest of the
 * codebase as much as WordPress's architecture allows:
 * - functions.php is NOT modified for this page's routing/SEO/menu at
 *   all — no page definition entry, no SEO array entry, no menu entry.
 *   WordPress's own template hierarchy picks THIS file automatically
 *   for any page whose slug is "womens-fight-academy". (functions.php
 *   only has one line, `require_once .../inc/academy-leads.php`, to
 *   load the separate lead system below — WordPress requires that file
 *   to be loaded on every request for its post type/admin-post hooks
 *   to register at all, so a single require line is the minimum
 *   possible touch.)
 * - style.css is NOT modified — every .wfa-* rule below is injected
 *   by this file alone, on this page's own wp_head hook, and applies
 *   nowhere else.
 * - The browser-tab title is set by this file's own
 *   pre_get_document_title filter, not functions.php's
 *   womensfight_seo_title(). (The meta description still falls back to
 *   functions.php's generic one, since avoiding a duplicate <meta
 *   name="description"> tag without editing functions.php isn't
 *   possible.)
 * - The "Free Counseling" form below submits to inc/academy-leads.php's
 *   own handler (action=wfa_submit_academy_form) and its own "wfa_lead"
 *   post type — completely separate from womensfight_lead_fields() /
 *   womensfight_render_customer_form() / the wf_project pipeline used
 *   by the rest of the site. Academy leads only ever appear under their
 *   own "Academy Leads" admin menu + dashboard, never mixed into
 *   Client Projects.
 * - Not in womensfight_menu_structure() (nav menu), by design.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'pre_get_document_title',
	function ( $title ) {
		return "Women's Fight Academy | নারীদের Computer Training";
	}
);

add_action(
	'wp_head',
	function () {
		?>
<style>
/* Women's Fight Academy — scoped to this page only, injected inline by page-womens-fight-academy.php */
.wfa-path-mock{width:100%; max-width:420px; display:flex; flex-direction:column; gap:14px;}
.wfa-path-mock-item{display:flex; align-items:center; gap:16px; background:var(--surface); border:1px solid var(--border); border-radius:18px; padding:18px 20px; box-shadow:var(--shadow);}
.wfa-path-mock-item b{display:block; font-size:.94rem; font-weight:700; color:var(--ink);}
.wfa-path-mock-item span{font-size:.8rem; color:var(--ink-faint);}

.wfa-trust-strip{display:flex; flex-wrap:wrap; justify-content:space-between; gap:16px; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-md); padding:22px 26px;}
.wfa-trust-item{display:flex; align-items:center; gap:10px; font-size:.86rem; font-weight:700; color:var(--ink-soft); flex:1; min-width:180px;}
.wfa-trust-item svg{width:20px; height:20px; stroke:var(--pink-light); fill:none; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round; flex:none;}

.wfa-course-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:22px;}
.wfa-course-grid .pkg h3{margin-top:2px;}

.wfa-placeholder-grid{display:grid; grid-template-columns:repeat(4,1fr); gap:18px;}
.wfa-placeholder{background:var(--surface); border:1px dashed var(--border); border-radius:18px; padding:32px 18px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; text-align:center; min-height:160px;}
.wfa-placeholder svg{width:30px; height:30px; stroke:var(--ink-faint); fill:none; stroke-width:1.6; stroke-linecap:round; stroke-linejoin:round;}
.wfa-placeholder span{font-size:.88rem; font-weight:700; color:var(--ink-soft);}
.wfa-placeholder em{font-style:normal; font-size:.76rem; color:var(--ink-faint); background:var(--bg-2); padding:3px 11px; border-radius:999px;}

.wfa-sample-note{background:rgba(0,191,253,.1); border:1px solid rgba(0,191,253,.3); border-radius:14px; padding:14px 18px; font-size:.86rem; color:var(--blue-light); font-weight:600; max-width:720px; margin:0 auto 20px;}
.wfa-form-result{font-size:.86rem; font-weight:700; color:var(--blue-light); margin-top:-4px;}

@media (max-width:980px){
  .wfa-course-grid{grid-template-columns:1fr 1fr;}
  .wfa-placeholder-grid{grid-template-columns:1fr 1fr;}
  .wfa-trust-strip{justify-content:flex-start;}
}
@media (max-width:640px){
  .wfa-course-grid{grid-template-columns:1fr;}
  .wfa-placeholder-grid{grid-template-columns:1fr;}
  .wfa-trust-item{min-width:100%;}
}
</style>
		<?php
	}
);

get_header();
?>
<main>

<div class="hero wrap">
  <div class="hero-glow-1"></div><div class="hero-glow-2"></div>
  <div class="hero-grid">
    <div>
      <span class="eyebrow">Women's Fight Academy</span>
      <h1>দক্ষতা শিখে নিজের ক্যারিয়ার গড়ুন</h1>
      <p class="lead" style="font-weight:600; color:var(--ink);">নারীদের জন্য নিরাপদ ও সহায়ক পরিবেশে Computer Training</p>
      <p class="lead">আপনি Student, Homemaker, Job Seeker অথবা Entrepreneur&mdash;যে অবস্থাতেই থাকুন, সঠিক Computer Skill আপনাকে চাকরি, Freelancing এবং নিজের Business পরিচালনার নতুন সুযোগ তৈরি করতে পারে।</p>
      <div class="hero-ctas">
        <a class="btn btn-primary" href="#wfa-counseling-form">ফ্রি কাউন্সেলিং নিন</a>
        <a class="btn btn-ghost" href="#wfa-courses">কোর্সগুলো দেখুন</a>
      </div>
    </div>
    <div class="hero-visual">
      <div class="wfa-path-mock">
        <div class="wfa-path-mock-item"><div class="ico sm"><svg><use href="#i-form"/></svg></div><div><b>Computer Operator</b><span>Office-ready Skill</span></div></div>
        <div class="wfa-path-mock-item"><div class="ico sm"><svg><use href="#i-edit"/></svg></div><div><b>Graphic Design</b><span>Creative &amp; Freelancing</span></div></div>
        <div class="wfa-path-mock-item"><div class="ico sm"><svg><use href="#i-megaphone"/></svg></div><div><b>Digital Marketing</b><span>Business &amp; Client Work</span></div></div>
      </div>
    </div>
  </div>
</div>

<section class="tight wrap">
  <div class="wfa-trust-strip">
    <div class="wfa-trust-item"><svg><use href="#i-users"/></svg><span>অভিজ্ঞ Trainer</span></div>
    <div class="wfa-trust-item"><svg><use href="#i-layers"/></svg><span>হাতে-কলমে Practical Class</span></div>
    <div class="wfa-trust-item"><svg><use href="#i-shield"/></svg><span>নারীবান্ধব Learning Environment</span></div>
    <div class="wfa-trust-item"><svg><use href="#i-route"/></svg><span>Career Guideline</span></div>
    <div class="wfa-trust-item"><svg><use href="#i-doc"/></svg><span>Course Completion Certificate</span></div>
  </div>
</section>

<section class="tight alt"><div class="wrap">
  <div class="section-head">
    <span class="eyebrow">কাদের জন্য</span>
    <h2>এই Training Program কাদের জন্য?</h2>
  </div>
  <div class="feat-grid">
    <div class="feat"><div class="ico sm"><svg><use href="#i-users"/></svg></div><h3>Female Student</h3><p>পড়াশোনার পাশাপাশি ভবিষ্যতের জন্য বাস্তব Computer Skill অর্জন করুন।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-target"/></svg></div><h3>Job Seeker</h3><p>চাকরির আবেদন ও Office-এর কাজের জন্য প্রয়োজনীয় দক্ষতা তৈরি করুন।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-heart"/></svg></div><h3>Homemaker</h3><p>ঘরে বসেই নতুন দক্ষতা শিখে নিজের একটা পরিচয় তৈরি করুন।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-bulb"/></svg></div><h3>Women Entrepreneur</h3><p>নিজের Business Online-এ সঠিকভাবে পরিচালনা করতে শিখুন।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-code"/></svg></div><h3>Freelancing-এ আগ্রহী নারী</h3><p>Freelancing শুরু করার জন্য প্রয়োজনীয় Skill ও Guideline পান।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-gear"/></svg></div><h3>Computer Skill শিখতে আগ্রহী নারী</h3><p>একদম শুরু থেকে ধাপে ধাপে Computer ব্যবহার শিখুন।</p></div>
  </div>
</div></section>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">সুযোগ</span>
    <h2>Computer Skill না থাকায় কি সুযোগ হাতছাড়া হচ্ছে?</h2>
  </div>
  <div class="feat-grid">
    <div class="feat"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>চাকরির আবেদন ও Office-এর কাজ</h3><p>Computer দক্ষতা না থাকায় আবেদন ও অফিসের কাজ করতে সমস্যা হয়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-bulb"/></svg></div><h3>আত্মবিশ্বাসের অভাব</h3><p>Computer ব্যবহারে অনভ্যস্ততার কারণে আত্মবিশ্বাসের ঘাটতি দেখা দেয়।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-cart"/></svg></div><h3>Business Online পরিচালনা</h3><p>নিজের Business Online-এ ঠিকভাবে পরিচালনা করতে না পারা।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-route"/></svg></div><h3>সঠিক Guideline-এর অভাব</h3><p>Freelancing শুরু করার সঠিক দিকনির্দেশনা না পাওয়া।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-shield"/></svg></div><h3>নিরাপদ পরিবেশের প্রয়োজন</h3><p>Digital Skill শেখার জন্য নিরাপদ ও সহায়ক পরিবেশের দরকার।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-target"/></svg></div><h3>Career Path অস্পষ্ট</h3><p>শেখার পর কোন Career Path অনুসরণ করবেন তা না জানা।</p></div>
  </div>
  <p class="note" style="margin-top:20px;">সঠিক Training দিয়ে এই বাধাগুলো ধাপে ধাপে দূর করা সম্ভব &mdash; নিচের কোর্সগুলো ঠিক এই লক্ষ্যেই সাজানো হয়েছে।</p>
</section>

<section class="tight alt" id="wfa-courses"><div class="wrap">
  <div class="section-head">
    <span class="eyebrow">কোর্স</span>
    <h2>যে তিনটি কোর্স করানো হয়</h2>
  </div>
  <div class="wfa-course-grid">
    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-form"/></svg></div>
      <h3>Computer Operator Course</h3>
      <p style="font-size:.88rem; color:var(--ink-soft);">একদম শুরু থেকে Computer শিখতে এবং Office Job-এর প্রস্তুতি নিতে চান যারা।</p>
      <ul>
        <li><svg><use href="#i-check"/></svg>Computer Fundamentals</li>
        <li><svg><use href="#i-check"/></svg>MS Word, Excel ও PowerPoint</li>
        <li><svg><use href="#i-check"/></svg>বাংলা ও ইংরেজি Typing</li>
        <li><svg><use href="#i-check"/></svg>Email ও Internet</li>
        <li><svg><use href="#i-check"/></svg>Online Application</li>
        <li><svg><use href="#i-check"/></svg>CV ও Office Work</li>
      </ul>
      <a class="btn btn-ghost btn-block" href="#wfa-counseling-form">কোর্স সম্পর্কে জানুন</a>
    </div>
    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-edit"/></svg></div>
      <h3>Graphic Design</h3>
      <p style="font-size:.88rem; color:var(--ink-soft);">Creative কাজ, Portfolio এবং Freelancing বা Local Client-এর সঙ্গে কাজ করতে আগ্রহীদের জন্য।</p>
      <ul>
        <li><svg><use href="#i-check"/></svg>Photoshop ও Illustrator</li>
        <li><svg><use href="#i-check"/></svg>Social Media Design</li>
        <li><svg><use href="#i-check"/></svg>Logo ও Brand Design</li>
        <li><svg><use href="#i-check"/></svg>Banner, Flyer ও Business Card</li>
        <li><svg><use href="#i-check"/></svg>Portfolio তৈরি</li>
        <li><svg><use href="#i-check"/></svg>Client Work Guideline</li>
      </ul>
      <a class="btn btn-ghost btn-block" href="#wfa-counseling-form">কোর্স সম্পর্কে জানুন</a>
    </div>
    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-megaphone"/></svg></div>
      <h3>Digital Marketing</h3>
      <p style="font-size:.88rem; color:var(--ink-soft);">নিজের Business অথবা Client-এর Business Online-এ প্রচার করতে চান যারা।</p>
      <ul>
        <li><svg><use href="#i-check"/></svg>Facebook Page Management</li>
        <li><svg><use href="#i-check"/></svg>Content Planning</li>
        <li><svg><use href="#i-check"/></svg>Canva Design</li>
        <li><svg><use href="#i-check"/></svg>Facebook Marketing</li>
        <li><svg><use href="#i-check"/></svg>Lead Generation</li>
        <li><svg><use href="#i-check"/></svg>Customer Communication</li>
      </ul>
      <a class="btn btn-ghost btn-block" href="#wfa-counseling-form">কোর্স সম্পর্কে জানুন</a>
    </div>
  </div>
  <p class="note">Course Fee ও Batch Schedule জানতে Free Counseling নিন।</p>
</div></section>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">কেন আমরা</span>
    <h2>কেন Women's Fight Academy?</h2>
  </div>
  <div class="benefits">
    <div class="benefit"><div class="ico sm"><svg><use href="#i-heart"/></svg></div><h3>সম্মানজনক পরিবেশ</h3><p>নারীদের জন্য সম্মানজনক ও সহায়ক পরিবেশ।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-bulb"/></svg></div><h3>Beginner-friendly</h3><p>একদম শুরু থেকে সহজভাবে শেখানোর পদ্ধতি।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-layers"/></svg></div><h3>Practical Class</h3><p>হাতে-কলমে Practical Class-এর মাধ্যমে শেখা।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>বাস্তব Project</h3><p>বাস্তব Project-এর মাধ্যমে দক্ষতা অর্জন।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-users"/></svg></div><h3>Support Team</h3><p>Trainer ও Support Team-এর নিয়মিত Guideline।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-route"/></svg></div><h3>Career Roadmap</h3><p>কোর্স অনুযায়ী স্পষ্ট Career Roadmap।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-check"/></svg></div><h3>Training Partner</h3><p>Training Partner: Life Support IT Institute।</p></div>
  </div>
</section>

<section class="tight alt"><div class="wrap">
  <div class="section-head">
    <span class="eyebrow">Career Path</span>
    <h2>কোর্স শেষে কোন পথে এগোতে পারবেন?</h2>
  </div>
  <div class="timeline">
    <div class="tl-step"><div class="tl-num">১</div><div class="tl-body"><h3>Skill Learning</h3><p>নির্বাচিত কোর্সের মূল বিষয়গুলো ধাপে ধাপে শেখা।</p></div></div>
    <div class="tl-step"><div class="tl-num">২</div><div class="tl-body"><h3>Practical Project</h3><p>শেখা বিষয় নিয়ে বাস্তব Project-এ হাতে-কলমে চর্চা।</p></div></div>
    <div class="tl-step"><div class="tl-num">৩</div><div class="tl-body"><h3>Portfolio / Job Preparation</h3><p>নিজের কাজের একটি Portfolio বা CV প্রস্তুত করা।</p></div></div>
    <div class="tl-step"><div class="tl-num">৪</div><div class="tl-body"><h3>Internship / Local Client / Freelancing / নিজের Business</h3><p>আগ্রহ ও সুযোগ অনুযায়ী পরবর্তী ধাপে এগিয়ে যাওয়া।</p></div></div>
  </div>
  <p class="note" style="margin-top:20px;">ফলাফল শিক্ষার্থীর নিজের অনুশীলন, পরিশ্রম এবং প্রাপ্ত সুযোগের উপর নির্ভর করে &mdash; কোনো নির্দিষ্ট আয়, চাকরি বা ফলাফলের নিশ্চয়তা দেওয়া হয় না।</p>
</div></section>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">Learning Experience</span>
    <h2>ক্লাসের পরিবেশ ও শিক্ষার্থীদের কাজ</h2>
    <p>এই অংশটি প্রকৃত ছবি ও তথ্য যুক্ত হওয়ার জন্য প্রস্তুত রাখা হয়েছে &mdash; আপাতত এখানে কোনো কাল্পনিক নাম, রিভিউ বা ছবি ব্যবহার করা হয়নি।</p>
  </div>
  <div class="wfa-placeholder-grid">
    <div class="wfa-placeholder"><svg><use href="#i-grid"/></svg><span>বাস্তব Class Photos</span><em>শীঘ্রই যুক্ত হবে</em></div>
    <div class="wfa-placeholder"><svg><use href="#i-doc"/></svg><span>Student Projects</span><em>শীঘ্রই যুক্ত হবে</em></div>
    <div class="wfa-placeholder"><svg><use href="#i-users"/></svg><span>Trainer Introduction</span><em>শীঘ্রই যুক্ত হবে</em></div>
    <div class="wfa-placeholder"><svg><use href="#i-heart"/></svg><span>Female Student Testimonials</span><em>শীঘ্রই যুক্ত হবে</em></div>
  </div>
</section>

<section class="tight alt" id="wfa-counseling-form"><div class="wrap">
<?php if ( isset( $_GET['wfa_submitted'] ) && '1' === $_GET['wfa_submitted'] ) : ?>

  <div class="wf-cform-success">
    <p>ধন্যবাদ। আপনার তথ্য আমরা পেয়েছি। আমাদের টিম খুব দ্রুত আপনার সাথে যোগাযোগ করবে।</p>
  </div>

<?php else : ?>

  <div class="section-head center">
    <span class="eyebrow">ফ্রি Counseling</span>
    <h2>আপনার জন্য কোন Course উপযুক্ত জানুন</h2>
  </div>

  <form class="wf-cform" id="wfa-cform" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
    <input type="hidden" name="action" value="wfa_submit_academy_form">
    <input type="hidden" name="wfa_redirect" value="<?php echo esc_url( get_permalink() ); ?>">
    <?php wp_nonce_field( 'wfa_submit_academy_form', 'wfa_form_nonce' ); ?>
    <div class="frow">
      <div><label for="wfa_name">শিক্ষার্থীর নাম</label><input type="text" id="wfa_name" name="wfa_name" placeholder="আপনার নাম" required></div>
      <div><label for="wfa_mobile">Mobile Number</label><input type="tel" id="wfa_mobile" name="wfa_mobile" placeholder="01XXXXXXXXX" required pattern="^01[0-9]{9}$"></div>
    </div>
    <div class="frow">
      <div><label for="wfa_whatsapp">WhatsApp Number</label><input type="tel" id="wfa_whatsapp" name="wfa_whatsapp" placeholder="01XXXXXXXXX"></div>
      <div>
        <label for="wfa_status">বর্তমান অবস্থা</label>
        <select id="wfa_status" name="wfa_status" required>
          <option value="">সিলেক্ট করুন</option>
          <option>Student</option>
          <option>Homemaker</option>
          <option>Job Seeker</option>
          <option>Business Owner</option>
          <option>Other</option>
        </select>
      </div>
    </div>
    <div class="frow">
      <div>
        <label for="wfa_course">পছন্দের Course</label>
        <select id="wfa_course" name="wfa_course" required>
          <option value="">সিলেক্ট করুন</option>
          <option>Computer Operator Course</option>
          <option>Graphic Design</option>
          <option>Digital Marketing</option>
          <option>নিশ্চিত নই — Counseling-এ জানতে চাই</option>
        </select>
      </div>
      <div>
        <label for="wfa_location">পছন্দের Training Location</label>
        <select id="wfa_location" name="wfa_location">
          <option value="">সিলেক্ট করুন</option>
          <option>রামগঞ্জ ব্রাঞ্চ</option>
          <option>চাটখিল ব্রাঞ্চ</option>
        </select>
      </div>
    </div>
    <div class="frow">
      <div>
        <label for="wfa_batch">পছন্দের Batch Time</label>
        <select id="wfa_batch" name="wfa_batch">
          <option value="">সিলেক্ট করুন</option>
          <option>সকাল</option>
          <option>দুপুর</option>
          <option>বিকাল</option>
          <option>সন্ধ্যা</option>
          <option>যেকোনো সময়</option>
        </select>
      </div>
      <div>
        <label for="wfa_goal">লক্ষ্য</label>
        <select id="wfa_goal" name="wfa_goal">
          <option value="">সিলেক্ট করুন</option>
          <option>Job</option>
          <option>Freelancing</option>
          <option>Business</option>
          <option>Basic Computer Skill</option>
        </select>
      </div>
    </div>
    <div><label for="wfa_message">সংক্ষিপ্ত Message</label><textarea id="wfa_message" name="wfa_message" rows="3" placeholder="আপনার প্রশ্ন বা প্রয়োজন লিখুন (ঐচ্ছিক)"></textarea></div>
    <button class="btn btn-primary" type="submit">ফ্রি কাউন্সেলিং নিন</button>
  </form>

<?php endif; ?>
</div></section>

<section class="tight wrap">
  <div class="section-head">
    <span class="eyebrow">অভিভাবকদের জন্য</span>
    <h2>অভিভাবকদের জন্য প্রয়োজনীয় তথ্য</h2>
  </div>
  <div class="benefits">
    <div class="benefit"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>কী শেখানো হবে</h3><p>Computer Operator, Graphic Design বা Digital Marketing-এর নির্দিষ্ট Curriculum অনুযায়ী পাঠদান।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-shield"/></svg></div><h3>Training Environment</h3><p>নারীবান্ধব ও সহায়ক পরিবেশে ক্লাস পরিচালনা।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-chart"/></svg></div><h3>Course Progress</h3><p>কোর্স চলাকালীন অগ্রগতি সম্পর্কে জানার সুযোগ।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-users"/></svg></div><h3>Trainer Support</h3><p>প্রশ্ন বা সমস্যায় Trainer ও Support Team-এর সহায়তা।</p></div>
    <div class="benefit"><div class="ico sm"><svg><use href="#i-pin"/></svg></div><h3>Center Visit / Counseling</h3><p>Free Counseling-এর মাধ্যমে বিস্তারিত জেনে নেওয়ার সুযোগ।</p></div>
  </div>
</section>

<section class="tight alt"><div class="wrap">
  <div class="section-head">
    <span class="eyebrow">সাধারণ প্রশ্ন</span>
    <h2>যা জানতে চান</h2>
  </div>
  <div class="faq">
    <details open><summary>একদম Computer না জানলেও কি ভর্তি হতে পারব?</summary><p>হ্যাঁ, Computer Operator Course একদম শুরু থেকেই শেখানো হয় — আগে থেকে অভিজ্ঞতার প্রয়োজন নেই।</p></details>
    <details><summary>কোন Course আমার জন্য উপযুক্ত?</summary><p>আপনার লক্ষ্য ও বর্তমান দক্ষতা অনুযায়ী Free Counseling-এ আমরা সঠিক Course বেছে নিতে সাহায্য করব।</p></details>
    <details><summary>নারীদের জন্য আলাদা Batch আছে কি?</summary><p>সর্বশেষ তথ্য জানতে Free Counseling-এর মাধ্যমে আমাদের Team-এর সঙ্গে যোগাযোগ করুন।</p></details>
    <details><summary>Course শেষে Certificate পাওয়া যাবে?</summary><p>হ্যাঁ, কোর্স সম্পন্ন করার পর Course Completion Certificate দেওয়া হয়।</p></details>
    <details><summary>Freelancing শুরু করার Guideline দেওয়া হবে কি?</summary><p>হ্যাঁ, Graphic Design ও Digital Marketing কোর্সে Freelancing ও Client Work-এর Guideline অন্তর্ভুক্ত থাকে।</p></details>
    <details><summary>Class কোথায় অনুষ্ঠিত হবে?</summary><p>সর্বশেষ তথ্য জানতে Free Counseling-এর মাধ্যমে আমাদের Team-এর সঙ্গে যোগাযোগ করুন।</p></details>
    <details><summary>Course Fee এবং সময়কাল কত?</summary><p>সর্বশেষ তথ্য জানতে Free Counseling-এর মাধ্যমে আমাদের Team-এর সঙ্গে যোগাযোগ করুন।</p></details>
    <details><summary>অভিভাবক কি Training Center Visit করতে পারবেন?</summary><p>সর্বশেষ তথ্য জানতে Free Counseling-এর মাধ্যমে আমাদের Team-এর সঙ্গে যোগাযোগ করুন।</p></details>
  </div>
</div></section>

<section class="tight wrap"><div class="cta-band">
  <div><h2>আজকের শেখা Skill-ই হতে পারে আপনার আগামী দিনের শক্তি</h2><p>আপনার লক্ষ্য ও বর্তমান দক্ষতা অনুযায়ী কোন Course দিয়ে শুরু করা ভালো, তা জানতে আমাদের সঙ্গে Free Counseling করুন।</p></div>
  <div style="display:flex; gap:14px; flex-wrap:wrap;">
    <a class="btn btn-primary" href="#wfa-counseling-form">ফ্রি কাউন্সেলিং নিন</a>
    <a class="btn btn-ghost" href="https://wa.me/8801748133740?text=<?php echo rawurlencode( 'আসসালামু আলাইকুম, আমি Women\'s Fight Academy-এর কোর্স সম্পর্কে জানতে চাই।' ); ?>" target="_blank" rel="noopener">WhatsApp-এ কথা বলুন</a>
  </div>
</div></section>

</main>
<script>
(function(){
	var form = document.getElementById('wfa-cform');
	if (!form) { return; }
	form.addEventListener('submit', function(e){
		if (!form.checkValidity()) {
			e.preventDefault();
			form.reportValidity();
		}
	});
})();
</script>
<?php
get_footer();

/**
 * This page's WordPress Page (slug: womens-fight-academy) is created
 * manually in wp-admin → Pages, not via functions.php's page-sync
 * system — WordPress's template hierarchy picks this file up
 * automatically because the filename matches the slug.
 */
