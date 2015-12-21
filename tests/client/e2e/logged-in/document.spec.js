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
    var supportBtn;
    var supportCount;
    var supportChart;
    var opposeBtn;
    var opposeCount;
    var opposeChart;
    var supportOpposeButtons;

    beforeEach(function() {
      supportBtn = element(by.css('.doc-support-button'));
      supportCount = element(by.binding('doc.support'));
      supportChart = element(by.css('.support-chart .chart .support rect'));
      opposeBtn = element(by.css('.doc-oppose-button'));
      opposeCount = element(by.binding('doc.oppose'));
      opposeChart = element(by.css('.support-chart .chart .oppose rect'));
      supportOpposeButtons = element(by.css('.support-oppose-buttons'));
    });

    it('properly handles "supporting" a document', function() {
      supportBtn.click();

      // check that button states change
      expect(supportOpposeButtons.getText()).toMatch(/1 support/i);
      expect(supportOpposeButtons.getText()).toMatch(/0 oppose/i);

      // check that support count and chart change
      expect(supportCount.getText()).toBe('1');
      expect(supportChart.getAttribute('width')).toBe('100');
      expect(opposeCount.getText()).toBe('0');
      expect(opposeChart.getAttribute('width')).toBe('0');
    });

    it('properly handles "opposing" a document', function() {
      opposeBtn.click();

      // check that button states change
      expect(supportOpposeButtons.getText()).toMatch(/0 support/i);
      expect(supportOpposeButtons.getText()).toMatch(/1 oppose/i);

      // check that oppose count and chart change
      expect(supportCount.getText()).toBe('0');
      expect(supportChart.getAttribute('width')).toBe('0');
      expect(opposeCount.getText()).toBe('1');
      expect(opposeChart.getAttribute('width')).toBe('100');
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
