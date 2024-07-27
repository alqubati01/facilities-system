'use strict';

(function () {
  // Init custom option check
  window.Helpers.initCustomOptionCheck();

  // Bootstrap validation example
  //------------------------------------------------------------------------------------------

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const bsValidationForms = document.querySelectorAll('.needs-validation');

  // Loop over them and prevent submission
  Array.prototype.slice.call(bsValidationForms).forEach(function (form) {
    form.addEventListener(
      'submit',
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        } else {
          // Submit your form
          alert('Submitted!!!');
        }

        form.classList.add('was-validated');
      },
      false
    );
  });
})();
/**
 * Form Validation (https://formvalidation.io/guide/examples)
 * ? Primary form validation plugin for this template
 * ? In this example we've try to covered as many form inputs as we can.
 * ? Though If we've miss any 3rd party libraries, then refer: https://formvalidation.io/guide/examples/integrating-with-3rd-party-libraries
 */
//------------------------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const formValidationExamples = document.getElementById('formValidationExamples'),
      formValidationSelect2Ele = jQuery(formValidationExamples.querySelector('[name="products[]"]'));

    const fv = FormValidation.formValidation(formValidationExamples, {
      fields: {
        amount: {
          validators: {
            notEmpty: {
              message: 'إدخل مبلغ التسهيل'
            },
            regexp: {
              regexp: /^[0-9]+$/,
              message: 'يجب أن يتكون المبلغ من الأرقام فقط'
            }
          }
        },
        amount_in_writing: {
          validators: {
            notEmpty: {
              message: 'إدخل مبلغ التسهيل كتابتاً'
            },
            regexp: {
              regexp: /^[\u0621-\u064A ]+$/,
              message: 'يجب أن يتكون المبلغ كتابتاً من الحروف فقط'
            }
          }
        },
        details: {
          validators: {
            notEmpty: {
              enabled: true,
              message: 'ادخل التفصيل'
            },
            regexp: {
              regexp: /^[\u0621-\u064Aa-zA-Z0-9() ]+$/,
              message: 'يجب أن يتكون التفصيل من الحروف والأرقام و() فقط'
            }
          }
        },
        recipient: {
          validators: {
            notEmpty: {
              message: 'إدخل اسم المستفيد'
            },
            regexp: {
              regexp: /^[\u0621-\u064A0-9.() ]+$/,
              message: 'يجب أن يتكون اسم المستفيد من الحروف والأرقام و(). فقط'
            }
          }
        },
        'products[]': {
          validators: {
            notEmpty: {
              enabled: true,
              message: 'أختار منتجات'
            }
          }
        },
        reason: {
          validators: {
            notEmpty: {
              enabled: false,
              message: 'ادخل السبب'
            },
            regexp: {
              regexp: /^[\u0621-\u064A0-9.()\- ]+$/,
              message: 'يجب أن يتكون السبب من الحروف والأرقام و()-. فقط'
            }
          }
        },
        neighboring_customers: {
          validators: {
            regexp: {
              regexp: /^[\u0621-\u064A0-9.()\- ]+$/,
              message: 'يجب أن يتكون العملاء المجاورين من الحروف والأرقام و()-. فقط'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: function (field, ele) {
            // field is the field name & ele is the field element
            switch (field) {
              case 'amount':
              case 'amount_in_writing':
              case 'details':
              case 'recipient':
              case 'products[]':
              case 'reason':
              case 'neighboring_customers':
              default:
                return '.row';
            }
          }
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        // Submit the form when all fields are valid
        defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          //* Move the error message out of the `input-group` element
          if (e.element.parentElement.classList.contains('input-group')) {
            // `e.field`: The field name
            // `e.messageElement`: The message element
            // `e.element`: The field element
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
          //* Move the error message out of the `row` element for custom-options
          if (e.element.parentElement.parentElement.classList.contains('custom-option')) {
            e.element.closest('.row').insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    });

    var e = document.getElementById("type");
    var value = e.options[e.selectedIndex].value;

    if (value === '2') {
      fv
        .updateValidatorOption('details', 'notEmpty', 'enabled', false)
        .revalidateField('details');
    }

    var e2 = document.getElementById("reason_type");
    var value2 = e2.options[e2.selectedIndex].value;

    if (value2 === '2') {
      fv
        .updateValidatorOption('products[]', 'notEmpty', 'enabled', false)
        .revalidateField('products[]');

      fv
        .updateValidatorOption('reason', 'notEmpty', 'enabled', true)
        .revalidateField('reason');
    }

    formValidationExamples.querySelector('[name="type"]').addEventListener('change', function (e) {
      if (e.target.value === '2') {
        fv.disableValidator('details');
        console.log('disableValidator');
      } else {
        fv.enableValidator('details');
        console.log('enableValidator');
      }

      fv.revalidateField('details');
    });

    formValidationExamples.querySelector('[name="reason_type"]').addEventListener('change', function (e) {
      if (e.target.value === '2') { // 2 mean others
        fv.enableValidator('reason');
      } else {
        fv.disableValidator('reason');
      }

      fv.revalidateField('reason');
    });

    formValidationExamples.querySelector('[name="reason_type"]').addEventListener('change', function (e) {
      if (e.target.value === '1') { // 1 mean others
        fv.enableValidator('products[]');
      } else {
        fv.disableValidator('products[]');
      }

      fv.revalidateField('products[]');
    });
    //? Revalidation third-party libs inputs on change trigger

    // Select2 (Country)
    if (formValidationSelect2Ele.length) {
      formValidationSelect2Ele.wrap('<div class="position-relative"></div>');
      formValidationSelect2Ele
        .select2({
          placeholder: 'Select country',
          dropdownParent: formValidationSelect2Ele.parent()
        })
        .on('change.select2', function () {
          // Revalidate the color field when an option is chosen
          fv.revalidateField('products');
        });
    }

  })();
});
