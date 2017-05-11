// http://stackoverflow.com/a/37192700/738052
window.autoHeightTextarea = function(textarea) {
  $(textarea)
    .each(function () { adjustHeight(this); })
    .on('input', function () { adjustHeight(this); });

  function adjustHeight(ctrl) {
    $(ctrl).css({'height':'auto','overflow-y':'hidden'}).height(ctrl.scrollHeight);
  }
};

window.affixMarkdownToolbar = function (textareaSelector) {
  var $toolbar = $(textareaSelector).siblings('.editor-toolbar');
  var $editArea = $(textareaSelector).siblings('.CodeMirror-wrap');

  var bottomOfEditArea = $editArea.height() + $toolbar.offset().top;
  var offsetBottom = $(document).height() - bottomOfEditArea;

  var affixOpts = {
    offset: {
      top: $toolbar.offset().top,
      bottom: offsetBottom
    }
  };

  $toolbar.affix(affixOpts);
};
