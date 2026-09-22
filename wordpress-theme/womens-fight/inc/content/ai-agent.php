<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
return <<<'HTML'
<div class="page-header wrap"><div class="inner">
  <div class="ico"><svg><use href="#i-target"/></svg></div>
  <span class="eyebrow">AI Agent</span>
  <h1>লোকেশন অনুযায়ী অডিয়েন্স ক্যালকুলেটর</h1>
  <p>বিজ্ঞাপন ক্যাম্পেইন প্ল্যান করার আগে জেনে নিন — আপনার নির্বাচিত বিভাগ, বয়স, লিঙ্গ ও প্ল্যাটফর্মে সম্ভাব্য কত মানুষের কাছে পৌঁছানো যাবে।</p>
</div></div>
<section class="tight wrap">
  <div class="calc-wrap">
    <div class="calc-card">
      <h3><div class="ico sm"><svg><use href="#i-pin"/></svg></div> অডিয়েন্স তথ্য দিন</h3>
      <div class="calc-field">
        <label for="calcLocation">লোকেশন (বিভাগ)</label>
        <select id="calcLocation">
          <option value="dhaka_metro">ঢাকা মহানগর</option>
          <option value="dhaka" selected>ঢাকা বিভাগ</option>
          <option value="chattogram">চট্টগ্রাম বিভাগ</option>
          <option value="rajshahi">রাজশাহী বিভাগ</option>
          <option value="khulna">খুলনা বিভাগ</option>
          <option value="barishal">বরিশাল বিভাগ</option>
          <option value="sylhet">সিলেট বিভাগ</option>
          <option value="rangpur">রংপুর বিভাগ</option>
          <option value="mymensingh">ময়মনসিংহ বিভাগ</option>
          <option value="national">সমগ্র বাংলাদেশ</option>
        </select>
      </div>
      <div class="calc-field">
        <label for="calcPlatform">প্ল্যাটফর্ম</label>
        <select id="calcPlatform">
          <option value="facebook">Facebook</option>
          <option value="youtube">YouTube</option>
          <option value="instagram">Instagram</option>
          <option value="tiktok">TikTok</option>
        </select>
      </div>
      <div class="calc-field">
        <label>বয়সসীমা (একাধিক বেছে নিন)</label>
        <div class="calc-checks" id="calcAges">
          <label class="chip-check"><input type="checkbox" value="13-17"><span>১৩–১৭</span></label>
          <label class="chip-check"><input type="checkbox" value="18-24" checked><span>১৮–২৪</span></label>
          <label class="chip-check"><input type="checkbox" value="25-34" checked><span>২৫–৩৪</span></label>
          <label class="chip-check"><input type="checkbox" value="35-44" checked><span>৩৫–৪৪</span></label>
          <label class="chip-check"><input type="checkbox" value="45-54"><span>৪৫–৫৪</span></label>
          <label class="chip-check"><input type="checkbox" value="55+"><span>৫৫+</span></label>
        </div>
      </div>
      <div class="calc-field">
        <label for="calcGender">লিঙ্গ</label>
        <select id="calcGender">
          <option value="all" selected>সবাই</option>
          <option value="male">পুরুষ</option>
          <option value="female">নারী</option>
        </select>
      </div>
      <button class="btn btn-primary btn-block" id="calcRun"><svg style="width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2"><use href="#i-target"/></svg> অডিয়েন্স হিসাব করুন</button>
    </div>

    <div class="calc-result">
      <span class="headline">সম্ভাব্য অ্যাড অডিয়েন্স</span>
      <div class="number" id="calcNumber">০</div>
      <p class="sub" id="calcSub">বাম পাশের তথ্য দিয়ে হিসাব করুন।</p>
      <div class="funnel" id="calcFunnel"></div>
      <div class="calc-assump">
        <b>যেভাবে হিসাব করা হয়:</b> মোট জনসংখ্যা → ইন্টারনেট ও সোশ্যাল মিডিয়া ব্যবহারকারী (আনুমানিক ৪০%) → নির্বাচিত প্ল্যাটফর্মের ব্যবহারকারী → নির্বাচিত বয়স ও লিঙ্গের অংশ। জনসংখ্যার তথ্য বাংলাদেশ জনশুমারি ২০২২-এর উপর ভিত্তি করে আনুমানিক ও গোলাকৃত। এটি একটি পরিকল্পনা-সহায়ক অনুমান — Facebook Ads Manager বা Google Ads-এর প্রকৃত অডিয়েন্স সংখ্যার সাথে সম্পূর্ণ মিলবে না।
      </div>
    </div>
  </div>
</section>
<section class="tight wrap"><div class="cta-band"><div><h2>হিসাব দেখে ক্যাম্পেইন প্ল্যান করতে চান?</h2><p>এই সংখ্যাগুলো নিয়ে আমাদের স্ট্র্যাটেজি টিমের সাথে কথা বলুন, বাস্তব ক্যাম্পেইন প্ল্যান তৈরি করি।</p></div><a class="btn btn-primary" href="##LINK:contact##">যোগাযোগ করুন</a></div></section>
HTML;
