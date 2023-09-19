describe('Registration test', () => {
  const testEmailAddress = `test.12345@${Cypress.env("MAILISK_NAMESPACE")}.mailisk.net`;
  it('passes', () => {
    cy.visit('127.0.0.1:8000/register')
    cy.get('input[name="username"]').type('testing');
    cy.get('input[name="email"]').type(testEmailAddress);
    cy.get('input[name="password"]').type('12345678');
    cy.get('input[name="password_confirmation"]').type('12345678');

    cy.get('form').submit();

    cy.url().should('eq', 'http://127.0.0.1:8000/todos');

  })

})