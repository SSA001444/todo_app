describe('group creation', () => {
  const testEmailAddress = `test.12345@${Cypress.env("MAILISK_NAMESPACE")}.mailisk.net`;

  it('group creation', () => {
    cy.visit('127.0.0.1:8000/login')

    cy.get('input[name="email"]').type(testEmailAddress);
    cy.get('input[name="password"]').type('newpassword');

    cy.get('form').submit();

    cy.visit('127.0.0.1:8000/groups');

    cy.get('input[name="name"]').type('test group');
    cy.get('form').submit();

    cy.get('tbody').contains('tr','test group').should('exist');

    cy.get('a.btn-info').contains('Edit').click();

    cy.get('input[name="name"]').clear().type('Updated Group');

    cy.get('button[type="submit"]').click();

    cy.get('tbody').contains('tr','Updated Group').should('exist');

    cy.visit('127.0.0.1:8000/todos');

    cy.get('input[name="title"]').type('test todoGrouping');
    cy.get('input[name="commentary"]').type('test commentaryGrouping');
    cy.get('select[id="group_id"]').select('Updated Group');
    cy.get('form').submit();

    cy.get('tbody').contains('tr','Updated Group').should('exist');

    cy.get('tbody tr', { timeout: 10000 }).first().find('button.delete-button').click();

    cy.get('tbody tr', { timeout: 0 }).should('not.exist');

    cy.visit('127.0.0.1:8000/groups');

    cy.get('tbody tr', { timeout: 10000 }).first().find('a.btn-danger').click();

    cy.get('tbody tr', { timeout: 0 }).should('not.exist');

  });
})