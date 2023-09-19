describe('todo sharing', () => {
  const testEmailAddress = `test.12345@${Cypress.env("MAILISK_NAMESPACE")}.mailisk.net`;

  it('todo sharing', () => {

    cy.visit('127.0.0.1:8000/login')

    cy.get('input[name="email"]').type(testEmailAddress);
    cy.get('input[name="password"]').type('newpassword');

    cy.get('form').submit();

    cy.visit('127.0.0.1:8000/todos');

    cy.get('input[name="title"]').type('test todo sharing');
    cy.get('input[name="commentary"]').type('test commentary sharing');
    cy.get('form').submit();

    cy.get('tbody').contains('tr','test todo sharing').should('exist');

    cy.get('a.btn-info').contains('Share').click();

    cy.get('input[name="email"]').type('sskripka01@gmail.com');

    cy.get('button[type="submit"]').click();

    cy.get('a.nav-link').contains('Logout').click();

    cy.visit('127.0.0.1:8000/login')

    cy.get('input[name="email"]').type('sskripka01@gmail.com');
    cy.get('input[name="password"]').type('12345678');

    cy.get('form').submit();

    cy.get('tbody').contains('tr',testEmailAddress).should('exist');

    cy.get('tbody tr', { timeout: 10000 }).first().find('button.delete-button').click();

    cy.get('tbody tr', { timeout: 0 }).should('not.exist');

    cy.get('a.nav-link').contains('Logout').click();

    cy.visit('127.0.0.1:8000/login')

    cy.get('input[name="email"]').type(testEmailAddress);
    cy.get('input[name="password"]').type('newpassword');

    cy.get('form').submit();

    cy.visit('127.0.0.1:8000/todos');

    cy.get('tbody tr', { timeout: 10000 }).first().find('button.delete-button').click();

    cy.get('tbody tr', { timeout: 0 }).should('not.exist');

  });
})