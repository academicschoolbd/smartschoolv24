<?php /**
 * Shared form fields for every signup variant — bd.education-style layout.
 *
 * Expects:
 *   $with_package     : bool
 *   $packages         : array
 *   $domain_suffixes  : string[]
 *   $divisions        : stdClass[] from bd_geo_model->divisions()
 *   $boards           : map key=>label
 *   $designations     : map key=>label
 *   $institute_types  : map key=>label
 */
$suffixes      = isset($domain_suffixes) && is_array($domain_suffixes) && $domain_suffixes
    ? $domain_suffixes
    : ['smartschool.bd'];
$pickedSuffix  = set_value('domain_suffix') ?: $suffixes[0];
$divisions     = $divisions     ?? [];
$boards        = $boards        ?? [];
$designations  = $designations  ?? [];
$instTypes     = $institute_types ?? [];

$opt = function (array $map, $current, $placeholder) {
    $h = '<option value="">' . $placeholder . '</option>';
    foreach ($map as $k => $label) {
        $sel = ((string)$current === (string)$k) ? ' selected' : '';
        $h .= '<option value="' . html_escape($k) . '"' . $sel . '>' . $label . '</option>';
    }
    return $h;
};
?>
<?php if (validation_errors()): ?>
  <div class="bd-error">
    <strong>Please fix:</strong>
    <?= validation_errors('<ul><li>', '</li></ul>'); ?>
  </div>
<?php endif; ?>

<div class="bd-grid">
  <div class="bd-section">
    <span class="bd-section-icon" aria-hidden="true">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-6h6v6"/></svg>
    </span>
    <span class="bd-section-title">Institution<small lang="bn">প্রতিষ্ঠানের তথ্য</small></span>
  </div>
  <div class="bd-field">
    <label class="bd-label">School name <span class="req">*</span></label>
    <input class="bd-input" name="school_name" value="<?= set_value('school_name'); ?>"
           placeholder="e.g. Dhaka Model School" required>
  </div>
  <div class="bd-field">
    <label class="bd-label">School name (বাংলা)</label>
    <input class="bd-input" name="school_name_bn" value="<?= set_value('school_name_bn'); ?>"
           placeholder="ঢাকা মডেল স্কুল">
  </div>

  <div class="bd-field">
    <label class="bd-label">EIIN / EMIS code</label>
    <input class="bd-input" name="eiin_code" value="<?= set_value('eiin_code'); ?>"
           placeholder="6-digit EIIN (optional)" maxlength="32">
  </div>
  <div class="bd-field">
    <label class="bd-label">Type of institute</label>
    <select class="bd-select" name="institute_type">
      <?= $opt($instTypes, set_value('institute_type'), '— Select —'); ?>
    </select>
  </div>

  <div class="bd-section bd-section--pink">
    <span class="bd-section-icon" aria-hidden="true">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </span>
    <span class="bd-section-title">Contact person<small lang="bn">যোগাযোগের তথ্য</small></span>
  </div>
  <div class="bd-field">
    <label class="bd-label">Your name <span class="req">*</span></label>
    <input class="bd-input" name="owner_name" value="<?= set_value('owner_name'); ?>"
           placeholder="Headmaster / Principal" required>
  </div>
  <div class="bd-field">
    <label class="bd-label">Designation</label>
    <select class="bd-select" name="designation">
      <?= $opt($designations, set_value('designation'), '— Select —'); ?>
    </select>
  </div>

  <div class="bd-field">
    <label class="bd-label">WhatsApp / Mobile <span class="req">*</span></label>
    <input class="bd-input" name="owner_phone" value="<?= set_value('owner_phone'); ?>"
           placeholder="01XXXXXXXXX" required>
  </div>
  <div class="bd-field">
    <label class="bd-label">Official email <span class="req">*</span></label>
    <input class="bd-input" type="email" name="owner_email" value="<?= set_value('owner_email'); ?>"
           placeholder="you@school.edu.bd" required>
  </div>

  <div class="bd-section bd-section--amber">
    <span class="bd-section-icon" aria-hidden="true">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
    </span>
    <span class="bd-section-title">Academic &amp; Location<small lang="bn">শিক্ষাবোর্ড ও ঠিকানা</small></span>
  </div>
  <div class="bd-field">
    <label class="bd-label">Education board</label>
    <select class="bd-select" name="education_board">
      <?= $opt($boards, set_value('education_board'), '— Select —'); ?>
    </select>
  </div>
  <div class="bd-field">
    <label class="bd-label">Division</label>
    <select class="bd-select" name="division_id" id="bd-division">
      <option value="">— Select —</option>
      <?php $selDiv = set_value('division_id'); foreach ($divisions as $d): ?>
        <option value="<?= (int)$d->id; ?>" <?= ((string)$selDiv === (string)$d->id) ? 'selected' : ''; ?>>
          <?= html_escape($d->name); ?> <?= $d->bn_name ? ' / ' . $d->bn_name : ''; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="bd-field">
    <label class="bd-label">District</label>
    <select class="bd-select" name="district_id" id="bd-district" data-current="<?= set_value('district_id'); ?>">
      <option value="">— Select division first —</option>
    </select>
  </div>
  <div class="bd-field">
    <label class="bd-label">Upazila / Thana</label>
    <select class="bd-select" name="upazila_id" id="bd-upazila" data-current="<?= set_value('upazila_id'); ?>">
      <option value="">— Select district first —</option>
    </select>
  </div>
</div>

<div class="bd-section bd-section--green bd-section--standalone" style="margin-top: 18px;">
  <span class="bd-section-icon" aria-hidden="true">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></svg>
  </span>
  <span class="bd-section-title">Website domain<small lang="bn">আপনার ওয়েবসাইটের ঠিকানা</small></span>
</div>
<div class="bd-field bd-field-subdomain" style="margin-top: 8px;" data-bd-suffix-count="<?= count($suffixes); ?>" data-bd-pickone>
  <label class="bd-label">Short name / prefix for website domain <span class="req">*</span></label>
  <?php if (count($suffixes) > 1): ?>
    <p class="bd-subdomain-pick-one" lang="bn">
      <span class="bd-pick-icon" aria-hidden="true">❖</span>
      ওয়েবসাইট নাম নিচের যেকোন <strong>একটা</strong> পূরণ করুন প্রতিষ্ঠানের নামের সংক্ষিপ্ত রুপ দিয়ে
    </p>
    <div class="bd-subdomain-double">
      <?php $sub_old = [
          'subdomain_smartschool.bd' => set_value('subdomain_smartschool_bd'),
          'subdomain_institution.bd' => set_value('subdomain_institution_bd'),
      ]; ?>
      <?php foreach ($suffixes as $i => $sfx): ?>
        <?php $name = 'subdomain_' . str_replace('.', '_', $sfx); ?>
        <div class="bd-subdomain bd-subdomain--row" data-bd-suffix-row="<?= html_escape($sfx); ?>">
          <span class="bd-fix">www.</span>
          <input name="<?= html_escape($name); ?>"
                 pattern="[a-z0-9_-]{3,64}"
                 value="<?= html_escape(set_value($name)); ?>"
                 placeholder="example: dkmschool"
                 autocomplete="off"
                 maxlength="64"
                 data-bd-pickone-input
                 data-bd-suffix="<?= html_escape($sfx); ?>">
          <span class="bd-fix bd-fix--suffix">.<?= html_escape($sfx); ?></span>
          <span class="bd-availability" data-bd-availability hidden></span>
        </div>
        <?php if ($i === 0 && count($suffixes) > 1): ?>
          <span class="bd-or-badge bd-or-badge--vertical" aria-hidden="true">OR</span>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <div class="bd-availability-panel" data-bd-availability-panel hidden>
      <p class="bd-availability-msg" data-bd-availability-msg></p>
      <div class="bd-availability-suggestions" data-bd-availability-suggestions hidden>
        <span class="bd-availability-label">Try one of these:</span>
        <div class="bd-availability-chips" data-bd-availability-chips></div>
      </div>
    </div>
  <?php else: ?>
    <div class="bd-subdomain" data-bd-suffix-row="<?= html_escape($suffixes[0]); ?>">
      <span class="bd-fix">www.</span>
      <input name="subdomain" pattern="[a-z0-9_-]{3,64}" value="<?= set_value('subdomain'); ?>"
             placeholder="example: dkmschool" required autocomplete="off" maxlength="64"
             data-bd-subdomain
             data-bd-pickone-input
             data-bd-suffix="<?= html_escape($suffixes[0]); ?>">
      <span class="bd-fix bd-fix--suffix">.<?= html_escape($suffixes[0]); ?></span>
      <span class="bd-availability" data-bd-availability hidden></span>
      <input type="hidden" name="domain_suffix" value="<?= html_escape($suffixes[0]); ?>">
    </div>
    <div class="bd-availability-panel" data-bd-availability-panel hidden>
      <p class="bd-availability-msg" data-bd-availability-msg></p>
      <div class="bd-availability-suggestions" data-bd-availability-suggestions hidden>
        <span class="bd-availability-label">Try one of these:</span>
        <div class="bd-availability-chips" data-bd-availability-chips></div>
      </div>
    </div>
  <?php endif; ?>
  <small class="bd-hint">Lowercase letters, numbers, hyphens. Min 3 characters. <strong>Fill in exactly one</strong> — leave the other blank.</small>
  <p class="bd-hint bd-pickone-error" data-bd-pickone-error hidden style="color:#d6336c;font-weight:600;"></p>
  <!-- Live preview chip: updated in JS as the user types in either input.
       Mirrors the value being checked + the AJAX availability result. -->
  <div class="bd-domain-preview bd-domain-preview--empty" data-bd-preview>
    <span class="bd-domain-preview-label" lang="bn">আপনার ওয়েবসাইট হবে :</span>
    <span class="bd-domain-preview-url" data-bd-preview-url>your-name.<?= html_escape($suffixes[0]); ?></span>
    <span class="bd-domain-preview-status" data-bd-preview-status aria-live="polite"></span>
  </div>
</div>

<?php if (!empty($with_package) && !empty($packages)): ?>
  <div class="bd-field" style="margin-top: 14px;">
    <label class="bd-label bd-label-plans">Choose your plan <span class="req">*</span></label>
    <div class="bd-plans">
      <?php foreach ($packages as $p): ?>
        <label class="bd-plan">
          <input type="radio" name="package_id" value="<?= (int)$p->id; ?>"
                 <?= $p->is_default_trial ? 'checked' : ''; ?>>
          <strong><?= html_escape($p->name); ?></strong>
          <div class="bd-price">
            ৳<?= number_format((float)$p->price_bdt, 0); ?>
            <small><?= $p->billing_period === 'yearly' ? '/ yr' : '/ mo'; ?></small>
          </div>
          <small><?= html_escape($p->description); ?></small>
        </label>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<div class="bd-field" style="margin-top: 12px;">
  <label class="bd-label">Anything else? <small style="opacity:.7">(optional)</small></label>
  <textarea class="bd-input" name="notes" rows="2"
            placeholder="e.g. 350 students, two campuses, need parent SMS"><?= set_value('notes'); ?></textarea>
</div>

<label class="bd-check">
  <input type="checkbox" name="terms_accept" value="1" required>
  <span>I agree to the
    <a href="<?= base_url('terms'); ?>" target="_blank">Terms of Service</a>
    and <a href="<?= base_url('privacy'); ?>" target="_blank">Privacy Policy</a>.
  </span>
</label>

<p class="bd-hint" data-bd-extra-warn hidden style="color:#d6336c;font-weight:600;">
  Multiple subdomain fields detected. Only one will be used — please reload the page if this looks unexpected.
</p>

<div class="bd-submit-row">
  <button type="submit" name="submit" value="apply" class="bd-btn">
    সাবমিট করুন &nbsp;&middot;&nbsp;
    <?= !empty($with_package) ? 'Start trial' : 'Submit signup'; ?>
    <span class="bd-btn-arrow" aria-hidden="true">→</span>
  </button>
  <span class="bd-progress" data-bd-progress data-complete="false">
    <span class="bd-progress-ring" aria-hidden="true"></span>
    <span class="bd-progress-label">0% complete</span>
  </span>
</div>

<script>
(function(){
  // Cascading Division -> District -> Upazila dropdowns (vanilla JS).
  var BASE = <?= json_encode(rtrim(base_url(), '/') . '/signup'); ?>;
  var divEl = document.getElementById('bd-division');
  var disEl = document.getElementById('bd-district');
  var upaEl = document.getElementById('bd-upazila');
  if (!divEl || !disEl || !upaEl) return;

  function fillOptions(el, rows, placeholder, preselect){
    el.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = ''; opt.textContent = placeholder; el.appendChild(opt);
    rows.forEach(function(r){
      var o = document.createElement('option');
      o.value = r.id;
      o.textContent = r.name + (r.bn_name ? ' / ' + r.bn_name : '');
      if (preselect && String(preselect) === String(r.id)) o.selected = true;
      el.appendChild(o);
    });
  }

  function loadJson(url, cb){
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function(){
      if (xhr.readyState === 4) {
        if (xhr.status >= 200 && xhr.status < 300) {
          try { cb(JSON.parse(xhr.responseText) || []); }
          catch(e){ cb([]); }
        } else { cb([]); }
      }
    };
    xhr.send();
  }

  function loadDistricts(divisionId, preselect){
    if (!divisionId) {
      fillOptions(disEl, [], '— Select division first —', null);
      fillOptions(upaEl, [], '— Select district first —', null);
      return;
    }
    loadJson(BASE + '/ajax_districts/' + encodeURIComponent(divisionId), function(rows){
      fillOptions(disEl, rows, '— Select district —', preselect);
      if (preselect) loadUpazilas(preselect, upaEl.dataset.current);
      else fillOptions(upaEl, [], '— Select district first —', null);
    });
  }
  function loadUpazilas(districtId, preselect){
    if (!districtId) {
      fillOptions(upaEl, [], '— Select district first —', null);
      return;
    }
    loadJson(BASE + '/ajax_upazilas/' + encodeURIComponent(districtId), function(rows){
      fillOptions(upaEl, rows, '— Select upazila —', preselect);
    });
  }

  divEl.addEventListener('change', function(){ loadDistricts(divEl.value, null); });
  disEl.addEventListener('change', function(){ loadUpazilas(disEl.value, null); });

  // Initial load — preserve set_value() after a validation failure.
  if (divEl.value) loadDistricts(divEl.value, disEl.dataset.current || null);
})();
</script>
