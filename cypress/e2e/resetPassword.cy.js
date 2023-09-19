describe('reset password', () => {
  let resetLink;
  const testEmailAddress = `test.12345@${Cypress.env("MAILISK_NAMESPACE")}.mailisk.net`;


  it('passes', () => {
    cy.visit('127.0.0.1:8000/forget-password')

    cy.get('input[name="email"]').type(testEmailAddress);

    cy.get('form').submit();

    cy.mailiskSearchInbox(Cypress.env("MAILISK_NAMESPACE"), {
      to_addr_prefix: testEmailAddress,
    }).then((response) => {
      const emails = response.data;
      const email = emails[0];
      resetLink = email.text.match(/.*\[(http:\/\/127.0.0.1:8000\/reset-password\/.*)\].*/)[1];
      expect(resetLink).to.not.be.undefined;

      cy.visit(resetLink);
      cy.get('input[name="email"]').type(testEmailAddress);
      cy.get('input[name="password"]').type("newpassword");
      cy.get('input[name="password_confirmation"]').type("newpassword");
      cy.get("form").submit();


      cy.visit('127.0.0.1:8000/login')

      cy.get('input[name="email"]').type(testEmailAddress);
      cy.get('input[name="password"]').type('newpassword');

      cy.get('form').submit();

    });
  });

})