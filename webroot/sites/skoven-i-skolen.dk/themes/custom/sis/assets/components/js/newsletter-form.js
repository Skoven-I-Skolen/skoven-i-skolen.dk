function newsletterValidation(newsletterFormWrappers) {
  if (newsletterFormWrappers.length === 0) {
    return;
  }
  for (let i = 0; i < newsletterFormWrappers.length; i += 1) {
    const newsletterFormWrapper = newsletterFormWrappers[i];
    newsletterFormWrapper.classList.add('loaded');

    const formAction = newsletterFormWrapper.dataset.action;
    const formList = newsletterFormWrapper.dataset.list;
    const formGdprText = newsletterFormWrapper.dataset.gdprText;
    const formGdprLink = newsletterFormWrapper.dataset.gdprLink;

    // Create form element
    const form = document.createElement('form');
    form.setAttribute('action', formAction);
    form.setAttribute('method', 'post');
    form.setAttribute('class', 'newsletter-form js-newsletter-form');

    // Create hidden input for optin_scheme
    const optinSchemeInput = document.createElement('input');
    optinSchemeInput.setAttribute('type', 'hidden');
    optinSchemeInput.setAttribute('name', 'optin_scheme');
    optinSchemeInput.setAttribute('value', 'double');

    form.appendChild(optinSchemeInput);

    // Create hidden input for action
    const actionInput = document.createElement('input');
    actionInput.setAttribute('type', 'hidden');
    actionInput.setAttribute('name', 'action');
    actionInput.setAttribute('value', 'subscribe');

    form.appendChild(actionInput);

    // Create hidden input for list
    const listInput = document.createElement('input');
    listInput.setAttribute('type', 'hidden');
    listInput.setAttribute('name', 'lists');
    listInput.setAttribute('value', formList);

    form.appendChild(listInput);

    // Create hidden input for secret
    const secretInput = document.createElement('input');
    secretInput.setAttribute('type', 'hidden');
    secretInput.setAttribute('name', 'secret');
    secretInput.setAttribute('value', '');

    form.appendChild(secretInput);

    // Create field-items div
    const fieldItemsDiv = document.createElement('div');
    fieldItemsDiv.setAttribute('class', 'field-items');

    // Create field-email div
    const fieldEmailDiv = document.createElement('div');
    fieldEmailDiv.setAttribute('class', 'field-item field-email');

    // Create field-gdpr div
    const fieldGdprDiv = document.createElement('div');
    fieldGdprDiv.setAttribute('class', 'field-item field-gdpr');

    // Create field-gdpr label
    const fieldGdprLabel = document.createElement('label');
    fieldGdprLabel.setAttribute('for', 'gdpr_checkbox');

    // Create field-item div
    const fieldItemDiv = document.createElement('div');
    fieldItemDiv.setAttribute('class', 'field-item');

    // Create input for email
    const emailInput = document.createElement('input');
    emailInput.setAttribute('type', 'email');
    emailInput.setAttribute('name', 'email_address');
    emailInput.setAttribute('placeholder', 'E-mail adresse');
    emailInput.setAttribute('class', 'input');
    emailInput.setAttribute('required', 'true');

    fieldEmailDiv.appendChild(emailInput);

    // Create input for gdpr
    const gdprInput = document.createElement('input');
    gdprInput.setAttribute('type', 'checkbox');
    gdprInput.setAttribute('value', 'gdpr');
    gdprInput.setAttribute('class', 'checkbox');
    gdprInput.setAttribute('required', 'true');

    fieldGdprLabel.innerHTML = `<span></span>${formGdprText}<br /><a href="${formGdprLink}" target="_blank">Læs vores cookie- og datapolitik</a>`;
    fieldGdprLabel.prepend(gdprInput);
    fieldGdprDiv.appendChild(fieldGdprLabel);

    // Create submit input
    const submitInput = document.createElement('input');
    submitInput.setAttribute('type', 'submit');
    submitInput.setAttribute('value', 'Tilmeld');
    submitInput.setAttribute('class', 'button');

    fieldItemDiv.appendChild(submitInput);

    // Create Append input fields to the form
    fieldItemsDiv.appendChild(fieldEmailDiv);
    fieldItemsDiv.appendChild(fieldGdprDiv);
    fieldItemsDiv.appendChild(fieldItemDiv);
    form.appendChild(fieldItemsDiv);

    // Append form to the wrapper div
    newsletterFormWrapper.appendChild(form);
  }
}

Drupal.behaviors.newsletterForm = {
  attach(context, settings) {
    newsletterValidation(document.querySelectorAll('.js-newsletter-form__wrapper:not(.loaded)'));
  },
};
