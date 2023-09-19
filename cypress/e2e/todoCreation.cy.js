describe('todo creation', () => {
  const testEmailAddress = `test.12345@${Cypress.env("MAILISK_NAMESPACE")}.mailisk.net`;

  it('todo creation', () => {
    cy.visit('127.0.0.1:8000/login')

    cy.get('input[name="email"]').type(testEmailAddress);
    cy.get('input[name="password"]').type('newpassword');

    cy.get('form').submit();

    cy.visit('127.0.0.1:8000/todos');

    cy.get('input[name="title"]').type('test todo');
    cy.get('input[name="commentary"]').type('test commentary');
    cy.get('form').submit();

    cy.get('tbody').contains('tr','test todo').should('exist');

    cy.get('a.btn-info').contains('Edit').click();

    cy.get('input[name="title"]').clear().type('Updated Title');
    cy.get('input[name="commentary"]').clear().type('Updated Commentary');

    cy.get('button[type="submit"]').click();

    cy.get('tbody').contains('tr','Updated Title').should('exist');

    cy.get('tbody tr', { timeout: 10000 }).first().find('button.delete-button').click();

    cy.get('tbody tr', { timeout: 0 }).should('not.exist');

  });

})