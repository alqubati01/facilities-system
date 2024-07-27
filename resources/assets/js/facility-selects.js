/**
 * Selects & Tags
 */

'use strict';

$(function () {
  const selectPicker = $('.selectpicker'),
    select2 = $('.select2'),
    select2Icons = $('.select2-icons'),
    specialization = $('#specialization'),
    category = $('#category'),
    product = $('#product'),
    branch = $('#branch'),
    unit = $('#unit'),
    currency = $('#currency'),
    status = $('#status');

  // Bootstrap Select
  // --------------------------------------------------------------------
  if (selectPicker.length) {
    selectPicker.selectpicker();
  }

  // Select2
  // --------------------------------------------------------------------

  if (specialization.length) {
    specialization.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'اختر التخصص',
        dropdownParent: $this.parent()
      });
    });
  }

  if (category.length) {
    category.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'اختر الفئة',
        dropdownParent: $this.parent()
      });
    });
  }

  if (product.length) {
    product.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'اختر الصنف',
        dropdownParent: $this.parent()
      });
    });
  }

  if (branch.length) {
    branch.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'اختر الفرع',
        dropdownParent: $this.parent()
      });
    });
  }

  if (unit.length) {
    unit.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'اختر الوحدة',
        dropdownParent: $this.parent()
      });
    });
  }

  if (currency.length) {
    currency.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'اختر العملة',
        dropdownParent: $this.parent()
      });
    });
  }

  if (status.length) {
    status.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'اختر الحالة',
        dropdownParent: $this.parent()
      });
    });
  }

  // Select2 Icons
  if (select2Icons.length) {
    // custom template to render icons
    function renderIcons(option) {
      if (!option.id) {
        return option.text;
      }
      var $icon = "<i class='" + $(option.element).data('icon') + " me-2'></i>" + option.text;

      return $icon;
    }
    select2Focus(select2Icons);
    select2Icons.wrap('<div class="position-relative"></div>').select2({
      templateResult: renderIcons,
      templateSelection: renderIcons,
      escapeMarkup: function (es) {
        return es;
      }
    });
  }
});
