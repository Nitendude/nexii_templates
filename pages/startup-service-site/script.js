// Navbar scroll effect
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 20);
});

// Mobile navigation
document.querySelector('.hamburger')?.addEventListener('click', () => {
  navbar.classList.toggle('menu-open');
});

document.querySelectorAll('.nav-links a').forEach(link => {
  link.addEventListener('click', () => navbar.classList.remove('menu-open'));
});

// Lightweight analytics hooks. Replace this with GA/Plausible later if needed.
const trackEvent = (name) => {
  const events = JSON.parse(localStorage.getItem('nexiiTrackEvents') || '[]');
  events.push({ name, path: window.location.pathname, timestamp: new Date().toISOString() });
  localStorage.setItem('nexiiTrackEvents', JSON.stringify(events.slice(-80)));
  console.info('[Nexii track]', name);
};

document.querySelectorAll('[data-track]').forEach(item => {
  item.addEventListener('click', () => trackEvent(item.dataset.track));
});

// Booking preview modal
const calendarModal = document.getElementById('calendarModal');
const calendarOpeners = document.querySelectorAll('[data-calendar-open]');
const calendarClosers = document.querySelectorAll('[data-calendar-close]');

const openCalendar = () => {
  if (!calendarModal) return;
  calendarModal.classList.add('open');
  calendarModal.setAttribute('aria-hidden', 'false');
  document.body.classList.add('calendar-open');
  calendarModal.querySelector('.calendar-close')?.focus();
};

const closeCalendar = () => {
  if (!calendarModal) return;
  calendarModal.classList.remove('open');
  calendarModal.setAttribute('aria-hidden', 'true');
  document.body.classList.remove('calendar-open');
};

calendarOpeners.forEach(button => {
  button.addEventListener('click', openCalendar);
});

calendarClosers.forEach(button => {
  button.addEventListener('click', closeCalendar);
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && calendarModal?.classList.contains('open')) {
    closeCalendar();
  }
});

// Lead source tracking
const sourceParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
const params = new URLSearchParams(window.location.search);
const leadSource = sourceParams
  .map(key => `${key}:${params.get(key) || ''}`)
  .filter(item => !item.endsWith(':'))
  .join(' | ') || document.referrer || 'Direct';
const leadSourceInput = document.getElementById('leadSource');
if (leadSourceInput) leadSourceInput.value = leadSource;

// Fade-in observer
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.12 });
document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

// Stagger children in grids
document.querySelectorAll('.services-grid, .features-grid, .portfolio-grid, .testi-grid, .process-steps, .industry-grid, .pricing-grid, .case-grid').forEach(grid => {
  grid.querySelectorAll('.fade-in, .service-card, .feature-card, .portfolio-card, .testi-card, .process-step, .industry-card, .price-card, .case-card').forEach((el, i) => {
    el.style.transitionDelay = `${i * 0.08}s`;
  });
});

// Portfolio filters
document.querySelectorAll('.filter-btn').forEach(button => {
  button.addEventListener('click', () => {
    const filter = button.dataset.filter;
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    document.querySelectorAll('#portfolio .portfolio-card').forEach(card => {
      card.classList.toggle('is-hidden', filter !== 'all' && card.dataset.category !== filter);
    });
    trackEvent(`portfolio-filter-${filter}`);
  });
});

// FAQ accordion
document.querySelectorAll('.faq-item').forEach(item => {
  item.addEventListener('click', () => {
    item.classList.toggle('open');
  });
});

// Estimate quiz
const estimateNeed = document.getElementById('estimateNeed');
const estimateTimeline = document.getElementById('estimateTimeline');
const estimateComplexity = document.getElementById('estimateComplexity');
const estimateResult = document.getElementById('estimateResult');
const estimateNeedValue = document.getElementById('estimateNeedValue');
const estimateTimelineValue = document.getElementById('estimateTimelineValue');
const estimateComplexityValue = document.getElementById('estimateComplexityValue');
const estimateResultValue = document.getElementById('estimateResultValue');
const applyEstimate = document.getElementById('applyEstimate');
const leadForm = document.getElementById('leadForm');
const leadStatus = document.getElementById('leadStatus');

const updateEstimate = () => {
  if (!estimateNeed || !estimateResult) return;
  const need = estimateNeed.value;
  const complexity = estimateComplexity.value;
  const timeline = estimateTimeline.value;
  const ranges = {
    Website: { simple: 'PHP 35k-PHP 60k', standard: 'PHP 60k-PHP 95k', advanced: 'PHP 95k+' },
    Automation: { simple: 'PHP 55k-PHP 85k', standard: 'PHP 85k-PHP 140k', advanced: 'PHP 140k+' },
    'Custom System': { simple: 'PHP 120k-PHP 180k', standard: 'PHP 180k-PHP 300k', advanced: 'PHP 300k+' }
  };
  const urgency = timeline === 'Urgent' ? ' Rush timeline may affect scope and cost.' : '';
  estimateResult.textContent = `Estimated starting range: ${ranges[need][complexity]}.${urgency}`;
  if (estimateNeedValue) estimateNeedValue.value = need;
  if (estimateTimelineValue) estimateTimelineValue.value = timeline;
  if (estimateComplexityValue) estimateComplexityValue.value = complexity;
  if (estimateResultValue) estimateResultValue.value = estimateResult.textContent;
};

[estimateNeed, estimateTimeline, estimateComplexity].forEach(control => {
  control?.addEventListener('change', updateEstimate);
});
updateEstimate();

applyEstimate?.addEventListener('click', () => {
  updateEstimate();
  const requestType = leadForm?.querySelector('[name="request_type"]');
  const service = leadForm?.querySelector('[name="service"]');
  const message = leadForm?.querySelector('[name="message"]');

  if (requestType) requestType.value = 'Project Estimate';
  if (service && estimateNeed) {
    const serviceValues = {
      Website: 'Website Development',
      Automation: 'Business Automation',
      'Custom System': 'Custom System'
    };
    service.value = serviceValues[estimateNeed.value] || 'Not sure yet';
  }
  if (message && estimateResult) {
    const estimateSummary = [
      `Quotation estimate selected: ${estimateResult.textContent}`,
      `Primary need: ${estimateNeed.value}`,
      `Timeline: ${estimateTimeline.value}`,
      `Complexity: ${estimateComplexity.value}`,
      '',
      'Project details: '
    ].join('\n');

    if (!message.value.trim() || message.value.startsWith('Quotation estimate selected:')) {
      message.value = estimateSummary;
    }
  }

  leadForm?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  setTimeout(() => {
    leadForm?.querySelector('[name="name"]')?.focus();
  }, 450);

  if (leadStatus) {
    leadStatus.textContent = 'Estimate added. Complete your contact details, then send the quotation request.';
  }

  trackEvent('estimate-added-to-quotation');
});

// The lead form posts to FormSubmit in the background so visitors stay on this page.

leadForm?.addEventListener('submit', async (event) => {
  event.preventDefault();
  updateEstimate();

  const submitButton = leadForm.querySelector('[type="submit"]');
  if (submitButton) submitButton.disabled = true;
  if (leadStatus) {
    leadStatus.textContent = 'Sending your quotation request...';
  }

  try {
    const response = await fetch(leadForm.action, {
      method: 'POST',
      body: new FormData(leadForm),
      headers: { Accept: 'application/json' }
    });

    if (!response.ok) throw new Error('Form submission failed');

    leadForm.reset();
    updateEstimate();
    if (leadStatus) {
      leadStatus.textContent = 'Quotation request sent. We will reply to your inbox soon.';
    }
    trackEvent('lead-form-submitted');
  } catch (error) {
    if (leadStatus) {
      leadStatus.textContent = 'Sorry, the request could not be sent. Please email nexii.techautomations@yahoo.com directly.';
    }
    console.error(error);
    trackEvent('lead-form-submit-error');
  } finally {
    if (submitButton) submitButton.disabled = false;
  }
});

// Downloadable company profile
document.getElementById('downloadProfile')?.addEventListener('click', () => {
  const profile = [
    'Nexii Tech Automation - Company Profile',
    '',
    'Services: Website Development, Business Automation, Custom Systems, API Integrations',
    'Industries: Clinics, Logistics, Real Estate, Retail, Service Businesses',
    'Process: Consultation, Planning & Design, Development, Deployment',
    'Packages: Starter Website from PHP 35k, Automation Setup from PHP 55k, Custom Systems by quote',
    '',
    'Contact: nexii.techautomations@yahoo.com | Parañaque, Metro Manila'
  ].join('\n');
  const blob = new Blob([profile], { type: 'text/plain' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = 'nexii-tech-automation-company-profile.txt';
  link.click();
  URL.revokeObjectURL(url);
});

// Bar chart animation loop
setInterval(() => {
  document.querySelectorAll('.bar').forEach(bar => {
    const h = Math.random() * 70 + 25;
    bar.style.height = h + '%';
    bar.classList.toggle('active', h > 75);
  });
}, 2800);
