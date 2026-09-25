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
        <span class="eyebrow">শুরু করুন</span>
        <h2>ফ্রি কনসালটেশন বুক করুন</h2>
        <p>ফর্মটি পূরণ করুন, আমাদের টিম শীঘ্রই যোগাযোগ করবে।</p>
      </div>
      <div class="consult-points">
        <div><svg><use href="#i-check"/></svg>১ কার্যদিবসের মধ্যে রিপ্লাই</div>
        <div><svg><use href="#i-check"/></svg>কোনো বাধ্যবাধকতা নেই</div>
        <div><svg><use href="#i-check"/></svg>প্রথম দিন থেকেই ডেডিকেটেড লিড</div>
      </div>
      <div class="consult-info">
        <span><svg style="display:inline;width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px;"><use href="#i-pin"/></svg>City Plaza, Lift-3, Ramganj, Lakshmipur</span>
        <span><svg style="display:inline;width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px;"><use href="#i-mail"/></svg>Womansfight65@gmail.com</span>
        <span><svg style="display:inline;width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2;vertical-align:-3px;margin-right:6px;"><use href="#i-phone"/></svg>+880 1748-133740</span>
      </div>
      <div class="foot-social" style="margin-top:6px; --border:rgba(255,255,255,.35);">
        <a href="https://www.facebook.com/womensfight" aria-label="Facebook" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M14 9h3V6h-3a4 4 0 0 0-4 4v2H7v3h3v6h3v-6h3l1-3h-4v-2a1 1 0 0 1 1-1Z"></path></svg></a>
        <a href="https://www.instagram.com/womensfight" aria-label="Instagram" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1"></circle></svg></a>
        <a href="https://www.tiktok.com/@womensfight" aria-label="TikTok" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M14 3v10.8a3.7 3.7 0 1 1-3.2-3.67"></path><path d="M14 3c.6 2.4 2.3 4 4.6 4.3"></path></svg></a>
        <a href="https://www.youtube.com/@womensfight" aria-label="YouTube" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="6" width="18" height="12" rx="4"></rect><path d="M11 10.2 14.5 12 11 13.8Z" fill="currentColor" stroke="none"></path></svg></a>
      </div>
    </div>
    <form class="consult-form" id="consultForm" method="post">
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
      <p class="form-msg" id="formMsg">ধন্যবাদ — এই ফর্মটি এখনো কোনো ইমেইল বা CRM-এর সাথে যুক্ত নয়। WordPress-এ Contact Form 7 বা WPForms প্লাগইন বসিয়ে এই ফর্মটিকে কার্যকর করে নিন।</p>
    </form>
  </div>
</section>
HTML;
