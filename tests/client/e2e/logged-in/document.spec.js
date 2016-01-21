var pages = require('../pages');

describe('Logged In - Document view', function() {
  var loginPage, docPage;

  beforeEach(function() {
    loginPage = new pages.Login();
    docPage = new pages.Document();

    loginPage.loginUser();
    docPage.get();
  });

  afterEach(function() {
    loginPage.logout();
  });

  describe('Supporting / Opposing document', function() {
    it('properly handles "supporting" a document', function() {
      docPage.buttons.support.click();

      // check that button states change
      expect(docPage.buttons.support.getText()).toMatch(/1 support/i);
      expect(docPage.buttons.oppose.getText()).toMatch(/0 oppose/i);

      // check that support count and chart change
      expect(docPage.stats.supportCount.getText()).toBe('1');
      expect(docPage.stats.supportChart.getAttribute('width')).toBe('100');
      expect(docPage.stats.opposeCount.getText()).toBe('0');
      expect(docPage.stats.opposeChart.getAttribute('width')).toBe('0');
    });

    it('properly handles "opposing" a document', function() {
      docPage.buttons.oppose.click();

      // check that button states change
      expect(docPage.buttons.support.getText()).toMatch(/0 support/i);
      expect(docPage.buttons.oppose.getText()).toMatch(/1 oppose/i);

      // check that oppose count and chart change
      expect(docPage.stats.supportCount.getText()).toBe('0');
      expect(docPage.stats.supportChart.getAttribute('width')).toBe('0');
      expect(docPage.stats.opposeCount.getText()).toBe('1');
      expect(docPage.stats.opposeChart.getAttribute('width')).toBe('100');
    });
  });

  //describe('Document comments', function() {
  //  xit('handles adding a comment to a document');
  //  xit('handles replying to a comment on a document');
  //  xit('handles liking a comment');
  //  xit('handles liking a comment reply');
  //  xit('handles flagging a comment');
  //  xit('handles flagging a comment reply');
  //});

});
