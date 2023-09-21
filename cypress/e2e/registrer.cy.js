describe('Registration test', () => {
  let verificationLink;
  const testEmailAddress = `test.12345@${Cypress.env("MAILISK_NAMESPACE")}.mailisk.net`;
  it('passes', () => {
    cy.visit('127.0.0.1:8000/register')
    cy.get('input[name="username"]').type('testing');
    cy.get('input[name="email"]').type(testEmailAddress);
    cy.get('input[name="password"]').type('12345678');
    cy.get('input[name="password_confirmation"]').type('12345678');

    cy.get('form').submit();

    cy.contains('We send email verification on your email').should('exist');

    cy.mailiskSearchInbox(Cypress.env("MAILISK_NAMESPACE"), {
      to_addr_prefix: testEmailAddress,
    }).then((response) => {
      const emails = response.data;
      const email = emails[0];
      verificationLink = email.text.match(/.*\[(http:\/\/127.0.0.1:8000\/account\/verify\/.*)\].*/)[1];
      expect(verificationLink).to.not.be.undefined;

      cy.visit(verificationLink);

    })
  })
})