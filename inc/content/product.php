<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
return <<<'HTML'
<div class="page-header wrap"><div class="inner">
  <div class="ico"><svg><use href="#i-grid"/></svg></div>
  <span class="eyebrow">প্রোডাক্ট</span>
  <h1>আমাদের SaaS প্রোডাক্ট</h1>
  <p>মাসিক সাবস্ক্রিপশনে ব্যবহার করুন আমাদের তৈরি SaaS টুলগুলো — ব্যবসা পরিচালনা এক জায়গায়।</p>
</div></div>
<section class="tight wrap">
  <div class="product-grid">

    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-doc"/></svg></div>
      <h3>WF-Invoice</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">Invoice/Memo, PDF, Print, Paid ও Due হিসাব।</p>
      <div class="price">৳১০০<span>/মাস</span></div>
      <a class="btn btn-ghost btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

    <div class="pkg feat-pkg">
      <span class="pkg-badge">জনপ্রিয়</span>
      <div class="ico sm"><svg><use href="#i-users"/></svg></div>
      <h3>WF-Lead CRM</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">Facebook/Website Lead, Follow-up ও Customer Conversion।</p>
      <div class="price">৳২০০<span>/মাস</span></div>
      <a class="btn btn-primary btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-cart"/></svg></div>
      <h3>WF-Stock</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">Product Stock, Purchase, Sale ও Low-stock Alert।</p>
      <div class="price">৳২০০<span>/মাস</span></div>
      <a class="btn btn-ghost btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-chart"/></svg></div>
      <h3>WF-Cashbook</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">দৈনিক আয়-ব্যয়, Cash Balance ও Monthly Report।</p>
      <div class="price">৳১০০<span>/মাস</span></div>
      <a class="btn btn-ghost btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-check"/></svg></div>
      <h3>WF-Attendance</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">Staff/Student Attendance, Late ও Absence Report।</p>
      <div class="price">৳১৫০<span>/মাস</span></div>
      <a class="btn btn-ghost btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-form"/></svg></div>
      <h3>WF-Booking</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">Appointment, Course Counseling ও Service Booking।</p>
      <div class="price">৳১৫০<span>/মাস</span></div>
      <a class="btn btn-ghost btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-layers"/></svg></div>
      <h3>WF-Task</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">Team Task, Deadline, Progress ও Daily Work Report।</p>
      <div class="price">৳১৫০<span>/মাস</span></div>
      <a class="btn btn-ghost btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-edit"/></svg></div>
      <h3>WF-Content Planner</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">Facebook Content Calendar, Idea ও Publishing Status।</p>
      <div class="price">৳১০০<span>/মাস</span></div>
      <a class="btn btn-ghost btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

    <div class="pkg">
      <div class="ico sm"><svg><use href="#i-share"/></svg></div>
      <h3>WF-Quotation</h3>
      <p style="color:var(--ink-faint); font-size:.86rem;">Professional Quotation, Proposal ও Estimate তৈরি।</p>
      <div class="price">৳১০০<span>/মাস</span></div>
      <a class="btn btn-ghost btn-block" href="##LINK:lead-form##">Subscribe করুন</a>
    </div>

  </div>
  <p class="note">* এটি একটি ডেমো প্রোডাক্ট লিস্টিং — মূল্য ও ফিচার শীঘ্রই আপডেট হবে। প্রশ্ন থাকলে যোগাযোগ করুন।</p>
</section>
HTML;
