<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
return <<<'HTML'
<div class="page-header wrap"><div class="inner">
  <div class="ico"><svg><use href="#i-mail"/></svg></div>
  <span class="eyebrow">যোগাযোগ</span>
  <h1>চলুন কথা বলি</h1>
  <p>আপনার ব্র্যান্ড ও লক্ষ্য সম্পর্কে জানান — আমরা একটি পরিকল্পনা নিয়ে ফিরে আসব, বিক্রির চাপ নিয়ে নয়।</p>
</div></div>
<section class="tight wrap">
  <div class="consult">
    <div class="consult-side">
      <div>
        <span class="eyebrow" style="color:#fff;">শুরু করুন</span>
        <h2>ফ্রি কনসালটেশন বুক করুন</h2>
        <p>ফর্মটি পূরণ করুন, আমাদের টিম শীঘ্রই যোগাযোগ করবে।</p>
      </div>
      <div class="consult-points">
        <div><svg><use href="#i-check"/></svg>১ কার্যদিবসের মধ্যে রিপ্লাই</div>
        <div><svg><use href="#i-check"/></svg>কোনো বাধ্যবাধকতা নেই</div>
        <div><svg><use href="#i-check"/></svg>প্রথম দিন থেকেই ডেডিকেটেড লিড</div>
      </div>
      <div class="consult-info">
        <span><svg style="display:inline;width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px;"><use href="#i-pin"/></svg>ঢাকা, বাংলাদেশ</span>
        <span><svg style="display:inline;width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px;"><use href="#i-mail"/></svg>hello@womensfight.agency</span>
        <span><svg style="display:inline;width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px;"><use href="#i-phone"/></svg>+৮৮০ XXX-XXXXXX</span>
      </div>
    </div>
    <form class="consult-form" id="consultForm" method="post" action="/wp-admin/admin-post.php">
      <input type="hidden" name="action" value="womensfight_contact_submit">
      <input type="text" name="fwebsite" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;">
      <div class="frow">
        <div><label for="fname">নাম</label><input id="fname" name="fname" required placeholder="আপনার পূর্ণ নাম"></div>
        <div><label for="fcompany">কোম্পানির নাম</label><input id="fcompany" name="fcompany" placeholder="আপনার প্রতিষ্ঠানের নাম"></div>
      </div>
      <div class="frow">
        <div><label for="fservice">কোন সার্ভিস প্রয়োজন?</label>
          <select id="fservice" name="fservice">
            <option>ডিজিটাল মার্কেটিং</option>
            <option>ওয়েবসাইট ডেভেলপমেন্ট</option>
            <option>ল্যান্ডিং পেজ</option>
            <option>ভিডিও প্রোডাকশন</option>
            <option>AI Agency</option>
            <option>AI Agent (অডিয়েন্স ক্যালকুলেটর)</option>
            <option>অন্যান্য</option>
          </select>
        </div>
        <div><label for="fmobile">মোবাইল নম্বর</label><input id="fmobile" name="fmobile" type="tel" required placeholder="+৮৮০ ১XXX-XXXXXX"></div>
      </div>
      <div><label for="femail">ইমেইল</label><input id="femail" name="femail" type="email" required placeholder="you@example.com"></div>
      <div><label for="fmsg">আপনার প্রজেক্ট সম্পর্কে বলুন</label><textarea id="fmsg" name="fmsg" placeholder="আপনি কী অর্জন করতে চান?"></textarea></div>
      <button class="btn btn-primary" type="submit">কনসালটেশন রিকোয়েস্ট করুন</button>
      <p class="form-msg" id="formMsg"></p>
    </form>
  </div>
</section>
HTML;
