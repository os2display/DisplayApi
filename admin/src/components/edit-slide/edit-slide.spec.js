describe("Edit slide page tests", () => {

  it("It validates new slide", () => {
    cy.visit("/slide/new");
    cy.get("#save_slide")
      .invoke("text")
      .should("match", /^Gem slide/);
    cy.get("#save_slide").click();
    cy.get("#save_slide")
      .invoke("text")
      .should("match", /^Gem slide/);
    cy.get("#slideName").type("x");
    cy.get("#slideTemplate").select("Text-and-image");
    cy.get("#title").type("x");
    cy.get("#duration").type(1);
    cy.get("#box-align").select("Toppen");
    cy.get("#save_slide").click();
    cy.get("#save_slide").should("not.exist");
  });

  it("It validates already existing slide", () => {
    cy.visit("/slide/32");
    cy.get("#slideName").clear();
    cy.get("#save_slide")
      .invoke("text")
      .should("match", /^Gem slide/);
    cy.get("#save_slide").click();
    cy.get("#save_slide")
      .invoke("text")
      .should("match", /^Gem slide/);
    cy.get("#slideName").type("x");
    cy.get("#box-align").select("Toppen");
    cy.get("#save_slide").click();
    cy.get("#save_slide").should("not.exist");
  });

  it("It cancels already existing slide", () => {
    cy.visit("/slides/");
    cy.visit("/slide/32");
    cy.get("#slide_cancel").click();
    cy.get("#slide_cancel").should("not.exist");
    cy.get("h1")
      .invoke("text")
      .should("match", /^Slides/);
  });
  it("It cancels new slide", () => {
    cy.visit("/slides/");
    cy.visit("/slide/new");
    cy.get("#slide_cancel").click();
    cy.get("#slide_cancel").should("not.exist");
    cy.get("h1")
      .invoke("text")
      .should("match", /^Slides/);
  });
});
