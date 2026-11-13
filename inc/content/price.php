<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
return <<<'HTML'
<div class="page-header wrap"><div class="inner">
  <div class="ico"><svg><use href="#i-gift"/></svg></div>
  <span class="eyebrow">প্রাইস</span>
  <h1>সার্ভিস প্রাইস ও ডিটেইলস</h1>
  <p>আমাদের প্রতিটি সার্ভিসের মূল্য, বিস্তারিত এবং বিজনেস পলিসি এখানে পাবেন।</p>
</div></div>

<section class="tight wrap">
  <div class="section-head"><span class="eyebrow">মূল্য তালিকা</span><h2>সার্ভিস প্রাইস</h2></div>
  <div class="pkg-grid">
    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-bolt"/></svg></div>
      <h3>[সার্ভিসের নাম লিখুন]</h3>
      <div class="price">[মূল্য লিখুন] <span>[বিলিং সাইকেল, যেমন মাসিক/একবার]</span></div>
      <ul>
        <li><svg><use href="#i-check"/></svg>[যা যা অন্তর্ভুক্ত তা লিখুন]</li>
        <li><svg><use href="#i-check"/></svg>[আইটেম ২]</li>
        <li><svg><use href="#i-check"/></svg>[আইটেম ৩]</li>
      </ul>
      <a class="btn btn-ghost btn-block" href="##LINK:contact##">যোগাযোগ করুন</a>
    </div>
    <div class="pkg feat-pkg">
      <span class="pkg-badge">জনপ্রিয়</span>
      <div class="ico sm"><svg><use href="#i-target"/></svg></div>
      <h3>[সার্ভিসের নাম লিখুন]</h3>
      <div class="price">[মূল্য লিখুন] <span>[বিলিং সাইকেল]</span></div>
      <ul>
        <li><svg><use href="#i-check"/></svg>[আইটেম ১]</li>
        <li><svg><use href="#i-check"/></svg>[আইটেম ২]</li>
        <li><svg><use href="#i-check"/></svg>[আইটেম ৩]</li>
      </ul>
      <a class="btn btn-primary btn-block" href="##LINK:contact##">যোগাযোগ করুন</a>
    </div>
    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-brain"/></svg></div>
      <h3>[সার্ভিসের নাম লিখুন]</h3>
      <div class="price">[মূল্য লিখুন] <span>[বিলিং সাইকেল]</span></div>
      <ul>
        <li><svg><use href="#i-check"/></svg>[আইটেম ১]</li>
        <li><svg><use href="#i-check"/></svg>[আইটেম ২]</li>
        <li><svg><use href="#i-check"/></svg>[আইটেম ৩]</li>
      </ul>
      <a class="btn btn-ghost btn-block" href="##LINK:contact##">যোগাযোগ করুন</a>
    </div>
  </div>
</section>

<section class="tight alt"><div class="wrap">
  <div class="section-head"><span class="eyebrow">বিস্তারিত</span><h2>সার্ভিস ডিটেইলস</h2></div>
  <div class="feat-grid">
    <div class="feat"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>[শিরোনাম লিখুন]</h3><p>[বিস্তারিত বর্ণনা এখানে লিখুন]</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>[শিরোনাম লিখুন]</h3><p>[বিস্তারিত বর্ণনা এখানে লিখুন]</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-doc"/></svg></div><h3>[শিরোনাম লিখুন]</h3><p>[বিস্তারিত বর্ণনা এখানে লিখুন]</p></div>
  </div>
</div></section>

<section class="tight wrap">
  <div class="section-head"><span class="eyebrow">নীতিমালা</span><h2>বিজনেস পলিসি</h2></div>
  <div class="feat-grid">
    <div class="feat"><div class="ico sm"><svg><use href="#i-shield"/></svg></div><h3>পেমেন্ট পলিসি</h3><p>[পেমেন্ট সংক্রান্ত নীতি এখানে লিখুন — অগ্রিম/কিস্তি, পেমেন্ট মেথড ইত্যাদি]</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-shield"/></svg></div><h3>রিফান্ড পলিসি</h3><p>[রিফান্ড সংক্রান্ত নীতি এখানে লিখুন]</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-shield"/></svg></div><h3>ডেলিভারি টাইমলাইন</h3><p>[কাজ শুরু থেকে ডেলিভারি পর্যন্ত সময়সীমা এখানে লিখুন]</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-shield"/></svg></div><h3>টার্মস অ্যান্ড কন্ডিশনস</h3><p>[অন্যান্য শর্তাবলী এখানে লিখুন]</p></div>
  </div>
</section>

<section class="tight wrap"><div class="cta-band"><div><h2>মূল্য নিয়ে কথা বলতে চান?</h2><p>আপনার প্রয়োজন অনুযায়ী কাস্টম কোট পেতে যোগাযোগ করুন।</p></div><a class="btn btn-primary" href="##LINK:contact##">যোগাযোগ করুন</a></div></section>
HTML;
