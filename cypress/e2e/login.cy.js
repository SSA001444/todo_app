describe('login test', () => {
  it('passes', () => {
    cy.visit('127.0.0.1:8000/login')


    cy.get('input[name="email"]').type('newuser@example.com');
    cy.get('input[name="password"]').type('12345678');

    cy.get('form').submit();


    cy.url().should('eq', 'http://127.0.0.1:8000/todos');
  })
})