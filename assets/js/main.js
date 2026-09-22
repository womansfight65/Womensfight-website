(function () {
  /* ---------------- AI Agent : audience estimator ---------------- */
  var DIVISIONS = {
    dhaka_metro:  { label: 'ঢাকা মহানগর',      pop: 23000000 },
    dhaka:        { label: 'ঢাকা বিভাগ',        pop: 45700000 },
    chattogram:   { label: 'চট্টগ্রাম বিভাগ',    pop: 33200000 },
    rajshahi:     { label: 'রাজশাহী বিভাগ',     pop: 21400000 },
    khulna:       { label: 'খুলনা বিভাগ',       pop: 17300000 },
    barishal:     { label: 'বরিশাল বিভাগ',      pop: 8300000  },
    sylhet:       { label: 'সিলেট বিভাগ',       pop: 10100000 },
    rangpur:      { label: 'রংপুর বিভাগ',       pop: 16800000 },
    mymensingh:   { label: 'ময়মনসিংহ বিভাগ',    pop: 12600000 },
    national:     { label: 'সমগ্র বাংলাদেশ',     pop: 170000000 }
  };
  var PLATFORM_SHARE = { facebook: 0.92, youtube: 0.85, instagram: 0.35, tiktok: 0.30 };
  var PLATFORM_LABEL = { facebook: 'Facebook', youtube: 'YouTube', instagram: 'Instagram', tiktok: 'TikTok' };
  var AGE_SHARE = { '13-17': 0.10, '18-24': 0.20, '25-34': 0.24, '35-44': 0.18, '45-54': 0.13, '55+': 0.15 };
  var INTERNET_PENETRATION = 0.40;
  var GENDER_SHARE = { all: 1, male: 0.51, female: 0.49 };

  function fmtBn(n) {
    n = Math.round(n);
    var s = String(n);
    var lastThree = s.length > 3 ? s.slice(-3) : s;
    var rest = s.slice(0, s.length - 3);
    if (rest !== '') { lastThree = ',' + lastThree; }
    var withCommas = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + lastThree;
    var bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    return withCommas.replace(/[0-9]/g, function (d) { return bnDigits[parseInt(d, 10)]; });
  }

  function runCalculator() {
    var locSel = document.getElementById('calcLocation');
    var platSel = document.getElementById('calcPlatform');
    var genderSel = document.getElementById('calcGender');
    var ageBoxes = document.querySelectorAll('#calcAges input[type="checkbox"]:checked');
    if (!locSel || !platSel || !genderSel) return;

    var loc = DIVISIONS[locSel.value];
    var platform = platSel.value;
    var gender = genderSel.value;

    var ageShareSum = 0;
    var ageLabels = [];
    ageBoxes.forEach(function (box) {
      ageShareSum += AGE_SHARE[box.value] || 0;
      ageLabels.push(box.parentElement.querySelector('span').textContent);
    });
    if (ageBoxes.length === 0) { ageShareSum = 0; }

    var population = loc.pop;
    var internetUsers = population * INTERNET_PENETRATION;
    var platformUsers = internetUsers * PLATFORM_SHARE[platform];
    var afterAge = platformUsers * ageShareSum;
    var final = afterAge * GENDER_SHARE[gender];

    var numberEl = document.getElementById('calcNumber');
    var subEl = document.getElementById('calcSub');
    var funnelEl = document.getElementById('calcFunnel');
    if (!numberEl) return;

    numberEl.textContent = fmtBn(final);
    subEl.textContent = loc.label + ' — ' + PLATFORM_LABEL[platform] + (ageLabels.length ? ' — বয়স: ' + ageLabels.join(', ') : '') + (gender !== 'all' ? ' — ' + (gender === 'male' ? 'পুরুষ' : 'নারী') : '');

    funnelEl.innerHTML =
      '<div class="funnel-row"><span>মোট জনসংখ্যা (' + loc.label + ')</span><span>' + fmtBn(population) + '</span></div>' +
      '<div class="funnel-row"><span>ইন্টারনেট ও সোশ্যাল মিডিয়া ব্যবহারকারী (~' + Math.round(INTERNET_PENETRATION * 100) + '%)</span><span>' + fmtBn(internetUsers) + '</span></div>' +
      '<div class="funnel-row"><span>' + PLATFORM_LABEL[platform] + ' ব্যবহারকারী (~' + Math.round(PLATFORM_SHARE[platform] * 100) + '%)</span><span>' + fmtBn(platformUsers) + '</span></div>' +
      '<div class="funnel-row"><span>নির্বাচিত বয়সসীমা (~' + Math.round(ageShareSum * 100) + '%)</span><span>' + fmtBn(afterAge) + '</span></div>' +
      '<div class="funnel-row"><span>নির্বাচিত লিঙ্গ</span><span>' + fmtBn(final) + '</span></div>';
  }

  document.addEventListener('DOMContentLoaded', function () {

    /* mobile main-menu toggle */
    var menuToggle = document.getElementById('menuToggle');
    var navLinks = document.getElementById('navLinks');
    if (menuToggle && navLinks) {
      menuToggle.addEventListener('click', function () {
        var open = navLinks.classList.toggle('mobile-open');
        navLinks.style.cssText = open
          ? 'display:flex;position:fixed;top:64px;left:16px;right:16px;background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:14px;flex-direction:column;gap:4px;box-shadow:var(--shadow);z-index:70;max-height:75vh;overflow:auto;'
          : '';
      });
    }

    /* mobile submenu (Service ▾) tap-to-open */
    document.querySelectorAll('.links li.menu-item-has-children > a').forEach(function (a) {
      a.addEventListener('click', function (e) {
        if (window.innerWidth <= 980) {
          e.preventDefault();
          a.parentElement.classList.toggle('open');
        }
      });
    });

    /* desktop submenu: JS-driven show/hide with a short close delay instead of
       relying purely on CSS :hover, so briefly crossing the gap between the
       "Service" link and its dropdown (or a slightly diagonal mouse path)
       doesn't slam the menu shut before the visitor reaches it. */
    var canHover = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (canHover) {
      document.querySelectorAll('.links li.menu-item-has-children').forEach(function (li) {
        var closeTimer;
        li.addEventListener('mouseenter', function () {
          clearTimeout(closeTimer);
          document.querySelectorAll('.links li.menu-item-has-children.open').forEach(function (other) {
            if (other !== li) other.classList.remove('open');
          });
          li.classList.add('open');
        });
        li.addEventListener('mouseleave', function () {
          closeTimer = setTimeout(function () {
            li.classList.remove('open');
          }, 300);
        });
      });
    }

    /* contact form: submits for real to admin-post.php (see functions.php);
       this just reports the ?womensfight_sent=1/0 result on the way back. */
    var sentStatus = new URLSearchParams(window.location.search).get('womensfight_sent');
    if (sentStatus !== null) {
      var msg = document.getElementById('formMsg');
      if (msg) {
        msg.textContent = sentStatus === '1'
          ? 'ধন্যবাদ — আপনার বার্তা পাঠানো হয়েছে, আমরা শীঘ্রই যোগাযোগ করব।'
          : 'দুঃখিত, বার্তাটি পাঠানো যায়নি। সরাসরি ইমেইল বা ফোনে যোগাযোগ করুন, অথবা আবার চেষ্টা করুন।';
        msg.classList.add('show');
        if (sentStatus !== '1') { msg.style.color = 'var(--pink-light)'; }
      }
    }

    /* AI Agent calculator */
    var calcBtn = document.getElementById('calcRun');
    if (calcBtn) {
      calcBtn.addEventListener('click', runCalculator);
      runCalculator();
    }
  });
})();
