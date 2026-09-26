<?php
/**
 * The footer for Women's Fight Agency theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$logo_full = esc_url( get_template_directory_uri() . '/assets/img/logo-full.png' );
?>
<footer class="site-footer">
  <div class="wrap foot-top">
    <div class="foot-brand">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none;">
        <img src="<?php echo $logo_full; ?>" alt="Women's Fight">
      </a>
      <p>ঢাকাভিত্তিক একটি ক্রিয়েটিভ ও ডিজিটাল এজেন্সি — স্ট্র্যাটেজি, ডিজাইন, কনটেন্ট ও AI দিয়ে সাহসী ব্র্যান্ড তৈরি করি।</p>
      <div class="foot-social">
        <a href="https://www.facebook.com/womensfight" aria-label="Facebook" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M14 9h3V6h-3a4 4 0 0 0-4 4v2H7v3h3v6h3v-6h3l1-3h-4v-2a1 1 0 0 1 1-1Z"></path></svg></a>
        <a href="https://www.instagram.com/womensfight" aria-label="Instagram" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1"></circle></svg></a>
        <a href="https://www.tiktok.com/@womensfight" aria-label="TikTok" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M14 3v10.8a3.7 3.7 0 1 1-3.2-3.67"></path><path d="M14 3c.6 2.4 2.3 4 4.6 4.3"></path></svg></a>
        <a href="https://www.youtube.com/@womensfight" aria-label="YouTube" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="6" width="18" height="12" rx="4"></rect><path d="M11 10.2 14.5 12 11 13.8Z" fill="currentColor" stroke="none"></path></svg></a>
      </div>
    </div>
    <div class="foot-col">
      <h4>কোম্পানি</h4>
      <a href="<?php echo esc_url( womensfight_page_url( 'home' ) ); ?>">হোম</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'case-study' ) ); ?>">Case Study</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'portfolio' ) ); ?>">Portfolio</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'action-plan' ) ); ?>">Action Plan</a>
    </div>
    <div class="foot-col">
      <h4>সার্ভিস</h4>
      <a href="<?php echo esc_url( womensfight_page_url( 'digital-marketing' ) ); ?>">Digital Marketing</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'website-development' ) ); ?>">Website Development</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'landing-page' ) ); ?>">Landing Page</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'video-production' ) ); ?>">Video Production</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'ai-agency' ) ); ?>">AI Agency</a>
    </div>
    <div class="foot-col">
      <h4>টুলস</h4>
      <a href="<?php echo esc_url( womensfight_page_url( 'ai-agent' ) ); ?>">AI Agent — অডিয়েন্স ক্যালকুলেটর</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'package' ) ); ?>">Package</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'product' ) ); ?>">Product</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'contact' ) ); ?>">Contact Us</a>
      <a href="<?php echo esc_url( womensfight_page_url( 'lead-form' ) ); ?>">Lead Form</a>
    </div>
    <div class="foot-col">
      <h4>যোগাযোগ</h4>
      <span style="display:block; margin-bottom:12px; color:var(--ink-soft); font-size:.9rem;">City Plaza, Lift-3, Ramganj, Lakshmipur</span>
      <a href="mailto:Womansfight65@gmail.com">Womansfight65@gmail.com</a>
      <a href="tel:+8801748133740">+880 1748-133740</a>
    </div>
  </div>
  <div class="wrap foot-bottom">
    <span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Women&rsquo;s Fight Agency. সর্বস্বত্ব সংরক্ষিত।</span>
    <span>প্রাইভেসি পলিসি &middot; শর্তাবলী</span>
  </div>
</footer>

<a href="https://wa.me/8801748133740?text=<?php echo rawurlencode( 'আসসালামু আলাইকুম, আমি Women\'s Fight-এর সার্ভিস সম্পর্কে জানতে চাই।' ); ?>" class="wf-whatsapp-float" target="_blank" rel="noopener" aria-label="WhatsApp-এ চ্যাট করুন">
  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm5.8 14.09c-.24.68-1.4 1.32-1.93 1.38-.5.06-1.02.28-3.42-.71-2.9-1.19-4.76-4.09-4.9-4.28-.14-.19-1.16-1.55-1.16-2.95 0-1.4.73-2.08.99-2.37.26-.28.57-.35.76-.35.19 0 .38 0 .55.01.18.01.42-.07.65.5.24.58.82 2.01.9 2.15.07.15.12.32.02.51-.09.19-.14.31-.28.48-.14.16-.29.36-.42.49-.14.14-.28.29-.12.57.16.28.72 1.19 1.55 1.93 1.07.95 1.97 1.25 2.25 1.39.28.14.44.12.61-.07.16-.19.7-.81.88-1.09.19-.28.37-.23.62-.14.26.09 1.63.77 1.91.91.28.14.47.21.53.33.07.12.07.68-.17 1.36Z"></path></svg>
</a>

<?php wp_footer(); ?>
</body>
</html>
