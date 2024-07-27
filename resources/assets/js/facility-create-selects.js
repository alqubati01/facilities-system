'use strict';

$(function () {
  const
    products = $('#products'),
    specialization = $('#specialization'),
    category = $('#category');

  if (products.length) {
    products.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'اختر الأصناف',
        dropdownParent: $this.parent()
      });
    });
  }

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
});
