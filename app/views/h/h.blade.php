<div ng-app="h" ng-controller="AppController" class="ng-scope no_search" style="">

  <!-- Toolbar -->
  <div class="topbar" ng-class="frame.visible &amp;&amp; 'shown'">
    <div class="inner" ng-switch="auth.persona">
      <!-- ngSwitchWhen: null -->
      <!-- ngSwitchDefault:  --><div class="pull-right user-picker ng-isolate-scope ng-scope" ng-switch-default="" data-user-picker-model="auth.persona" data-user-picker-options="auth.personas">
      <form action="" method="POST" enctype="multipart/form-data" accept-charset="utf-8" class="ng-pristine ng-valid">
        <div class="dropdown" ng-show="model">
          <span role="button" class="dropdown-toggle ng-binding" data-toggle="dropdown">cmbirk<span class="provider ng-binding">/localhost</span></span>
          <ul class="dropdown-menu pull-right" role="menu">
            <!-- ngRepeat: option in options --><li ng-repeat="option in options" ng-click="model = options[$index]" class="ng-scope ng-binding">cmbirk/localhost</li>
            <li><a href="http://hypothes.is/contact/" target="_blank">Feedback</a></li>
            <li><a href="/docs/help" target="_blank">Help</a></li>
            <li><a href="/stream#?user=cmbirk" target="_blank">My Annotations</a></li>
            <li ng-click="model = null">Sign out</li>
          </ul>
        </div>
      </form>

    </div>
  </div>

  <!-- Searchbar -->
  <div class="search-container">
    <div ng-show="show_search" class="visual-search visual-container ng-hide"><div id="search"><div class="VS-search">
      <div class="VS-search-box-wrapper VS-search-box">
        <div class="VS-icon VS-icon-search"></div>
        <div class="VS-placeholder"></div>
        <div class="VS-search-inner"><div class="search_input ui-menu not_editing not_selected"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="ui-menu ui-autocomplete-input" autocomplete="off" style="z-index: auto;"><div class="VS-input-width-tester VS-interface" style="opacity: 0; top: -9999px; left: -9999px; position: absolute; white-space: nowrap;"></div></div></div>
        <div class="VS-icon VS-icon-cancel VS-cancel-search-box" title="clear search"></div>
      </div>
    </div></div></div>
    <div ng-hide="show_search" class="visual-container" ng-click="show_search=true">
     <div id="search" class="magnify-glass VS-search">
       <div class="VS-icon VS-icon-search"></div>
     </div>
   </div>
 </div>

 <!-- Account and Authentication -->
 <div class="sheet collapsed" ng-class="sheet.collapsed &amp;&amp; 'collapsed'" ng-init="sheet.collapsed = true">

  <span class="close" role="button" title="Close" ng-click="sheet.collapsed = true"></span>

  <div data-resettable="true" data-tab-reveal="['forgot','activate']" ng-model="sheet.tab" class="form-vertical tabbable ng-scope ng-pristine ng-valid"><ul class="nav nav-tabs"><li><a href="">Sign in</a></li><li><a href="">Create an account</a></li><li><a href="">Claim a username</a></li><li style="display: none;"><a href="">Activate</a></li><li style="display: none;"><a href="">Forgot</a></li></ul><div class="tab-content">
    <div data-authentication="auth" ng-submit="submit(this[$parent.sheet.tab])" class="ng-isolate-scope ng-scope">
      
      <!-- Login -->
      <form data-title="Sign in" data-value="login" class="tab-pane ng-pristine ng-invalid ng-invalid-required" name="login" novalidate="">

        <input type="text" name="username" value="" placeholder="Username" ng-model="model.username" ng-minlength="3" required="" autocapitalize="false" class="ng-pristine ng-invalid ng-invalid-required ng-valid-minlength">
        <span ng-show="login.username.$error.required" slow-validate="username" class="slow-validate">Please enter your username.</span>
        <span ng-show="login.username.$error.minlength" slow-validate="username" class="slow-validate ng-hide">Usernames are at least 3 characters.</span>

        <input type="password" name="password" value="" placeholder="Password" ng-model="model.password" required="" autocapitalize="false" autocorrect="false" class="ng-pristine ng-invalid ng-invalid-required">
        <span ng-show="login.password.$error.required" slow-validate="password" class="slow-validate">Please enter your password.</span>

        <input type="submit" name="login" value="Sign in" ng-disabled="!login.$valid" disabled="disabled">

      </form>
      <!-- / Login -->

      <!-- Register -->
      <form data-title="Create an account" data-value="register" class="tab-pane ng-pristine ng-invalid ng-invalid-required" name="register" novalidate="">

        <input type="text" name="username" value="" placeholder="Username" required="" autocapitalize="false" ng-model="model.username" ng-minlength="3" ng-maxlength="15" ng-pattern="/^[A-Za-z0-9._]+$/" class="ng-pristine ng-invalid ng-invalid-required ng-valid-maxlength ng-valid-minlength ng-valid-pattern">
        <span ng-show="register.username.$error.required" slow-validate="username" class="slow-validate">Please choose a username.</span>
        <span ng-show="register.username.$error.minlength" slow-validate="username" class="slow-validate ng-hide">Usernames must be at least 3 characters.</span>
        <span ng-show="register.username.$error.maxlength" slow-validate="username" class="slow-validate ng-hide">Usernames must be 15 characters at most.</span>
        <span ng-show="register.username.$error.pattern" slow-validate="username" class="slow-validate ng-hide">Only letters, numbers, underscore and dot are allowed. </span>

        <input type="email" name="email" value="" placeholder="Email" ng-model="model.email" required="" autocapitalize="false" class="ng-pristine ng-invalid ng-invalid-required ng-valid-email">
        <span ng-show="register.email.$error.email" slow-validate="email" class="slow-validate ng-hide">Is this an email address?</span>
        <span ng-show="register.email.$error.required" slow-validate="email" class="slow-validate">Please enter your email.</span>

        <input type="password" name="password" value="" placeholder="Password" required="" autocapitalize="false" autocorrect="false" ng-minlength="2" ng-model="model.password" class="ng-pristine ng-invalid ng-invalid-required ng-valid-minlength">
        <span ng-show="register.password.$error.required" slow-validate="password" class="slow-validate">Please enter a password.</span>
        <span ng-show="register.password.$error.minlength" slow-validate="password" class="slow-validate ng-hide">Passwords must be at least 2 characters.</span>

        <input type="submit" name="sign_up" value="Sign up" ng-disabled="!register.$valid" disabled="disabled">

      </form>
      <!-- / Register -->

      <!-- Claim a username -->
      <form data-title="Claim a username" data-value="claim" class="tab-pane ng-pristine ng-invalid ng-invalid-required" name="claim" novalidate="">

        <p>To activate your reserved username, please enter the email address you used.</p>
        <!--Get Validation Code-->      
        <input type="email" name="email" value="" placeholder="Email" required="" autocapitalize="false" ng-model="model.email" class="ng-pristine ng-invalid ng-invalid-required ng-valid-email">
        <span ng-show="forgot.email.$error.email" slow-validate="email" class="slow-validate ng-hide">Is this an email address?</span>
        <span ng-show="forgot.email.$error.required" slow-validate="email" class="slow-validate">Please enter your email.</span>

        <input type="submit" name="forgot" value="Send activation" ng-disabled="!claim.$valid" disabled="disabled">

      </form>
      <!-- / Claim a username -->

      <!-- Activate -->
      <form data-title="Activate" data-value="activate" class="tab-pane ng-pristine ng-invalid ng-invalid-required" name="activate" novalidate="" style="display: none;">

       <p>Find the validation code in the confirmation email you recieved. Enter it below, along with the password you would like to use for your account.</p>

       <input type="text" name="code" value="" placeholder="Activation Code" required="" autocorrect="false" autocapitalize="false" ng-model="model.code" class="ng-pristine ng-invalid ng-invalid-required">
       <span ng-show="activate.code.$error.required" slow-validate="code" class="slow-validate">Please enter your validation code.</span>

       <input type="password" name="password" value="" placeholder="New Password" required="" autocapitalize="false" autocorrect="false" ng-minlength="2" ng-model="model.password" class="ng-pristine ng-invalid ng-invalid-required ng-valid-minlength">
       <span ng-show="activate.password.$error.required" slow-validate="password" class="slow-validate">Please choose a password.</span>
       <span ng-show="activate.password.$error.minlength" slow-validate="password" class="slow-validate ng-hide">Passwords must be at least 2 characters.</span>

       <input type="submit" name="activate" value="Sign in" ng-disabled="!activate.$valid" disabled="disabled">

     </form>
     <!--/Claim a username-->

     <!-- Forgot -->
     <form data-title="Forgot" data-value="forgot" class="tab-pane ng-pristine ng-invalid ng-invalid-required" name="forgot" novalidate="" style="display: none;">

      <p>Forgotten password? Enter your email below to send a recovery email.</p>

      <input type="email" name="email" value="" placeholder="Email" required="" autocapitalize="false" ng-model="model.email" class="ng-pristine ng-invalid ng-invalid-required ng-valid-email">
      <span ng-show="forgot.email.$error.email" slow-validate="email" class="slow-validate ng-hide">Is this an email address?</span>
      <span ng-show="forgot.email.$error.required" slow-validate="email" class="slow-validate">Please enter your email.</span>

      <input type="submit" name="forgot" value="Send recovery email" ng-disabled="!forgot.$valid" disabled="disabled">

    </form>
    <!-- / Forgot -->

    
  </div>
  <footer ng-show="sheet.tab == 'login' || sheet.tab == 'register'" class="ng-hide">
    <ul>
      <li>
        <a href="" ng-click="sheet.tab = 'forgot'">Password help?</a>
      </li>
      <li>
        <a href="" ng-click="sheet.tab = 'activate'">I have an activation code.</a>
      </li>
    </ul>
  </footer>
</div></div>
</div>
<!-- / Account and Authentication -->

</div>
<!-- / Toolbar -->

<!-- Content -->
<div id="wrapper">
  <!-- Angular view -->
  <!-- ngView:  --><div ng-view="" class="ng-scope">
  <ul class="sliding-panels ng-scope">
    <li>
      <ul>
        <!-- ngRepeat: annotation in annotations track by annotation.id -->
      </ul>
    </li>
  </ul>

</div>

<!-- Bottombar -->
<div class="bottombar ng-hide" ng-class="frame.visible &amp;&amp; 'shown'" ng-show="notifications.length">
  <ul class="notifications">
    <!-- ngRepeat: notif in notifications -->
  </ul>
</div>
<div class="annotator-adder" style="display: none;"><button>Annotate</button></div></div>
<!-- / Content -->

<!-- Templates -->
<script type="text/ng-template" id="annotation.html">
<form name="form">
<div class="magicontrols pull-right" ng-hide="editing">
<!-- Timestamp -->
<fuzzytime ng-model="model.$viewValue.updated"></fuzzytime>

<!-- More actions -->
<div class="dropdown small show">
<span class="dropdown-toggle" role="button" title="More actions"></span>
<ul class="dropdown-menu pull-right" role="menu">
<li class="reply-icon"
ng-click="reply()">Reply</li>
<li class="clipboard-icon"
ng-show="auth.update"
ng-click="edit()">Edit</li>
<li class="x-icon"
ng-show="auth.delete"
ng-click="delete()">Delete…</li>
<li class="flag-icon"
ng-hide="auth.delete"
ng-click="flag()">Flag…</li>
</ul>
</div>
</div>

<!-- Privacy -->
<privacy ng-model="$parent.model.$modelValue.permissions"
ng-show="editing && action != 'delete'"
class="dropdown privacy pull-right"
name="privacy" />

<!-- Deletion notice -->
<span ng-show="!editing && model.$viewValue.deleted"
>Annotation deleted.</span>

<!-- Preface -->
<header ng-switch="editing && action">
<strong ng-switch-when="delete">You may provide an explanation here.</strong>
<!-- User -->
<strong ng-switch-default class="indicators">
<username ng-model="model.$modelValue.user"></username>
<span class="small vis-icon"
ng-show="form.privacy.$viewValue != 'Public'"></span>
<span class="small highlight-icon"
ng-hide="model.$viewValue.text || editing || model.$viewValue.deleted || model.$viewValue.tags.length"></span>
<span class="small comment-icon"
ng-hide="model.$viewValue.target.length || model.$viewValue.references"></span>
</strong>
</header>

<!-- Prompt -->
<!-- TODO: replace with placeholder on markdown elements? -->
<div ng-show="model.$viewValue.deleted && !editing">
<ng-switch on="model.$viewValue.text.length">
<div ng-switch-when="0">(no reason given)</div>
<div ng-switch-default>Reason:</div>
</ng-switch>
</div>

<!-- Body -->
<div ng-show="mode=='search' && !editing">
<markdown ng-model="$parent.model.$modelValue.highlightText"
ng-readonly="!$parent.editing"
class="body"
name="text" />
</div>
<div ng-hide="mode=='search' && !editing">
<markdown ng-model="$parent.model.$modelValue.text"
ng-readonly="!$parent.editing"
class="body"
name="text" />
</div>

<!-- Tip about Markdown -->
<span ng-hide="!editing" class="tip"><a href="https://en.wikipedia.org/wiki/Markdown" target="_blank">Markdown</a> is supported.</span>

<!-- Tags -->
<ul ng-readonly="!editing"
ng-model="model.$modelValue.tags"
name="tags"
class="tags"
placeholder="Add tags"
/>

<!-- Bottom controls -->
<div class="buttonbar" ng-show="editing">
<div class="annotator-controls">
<ng-switch on="action">
<button ng-switch-when="edit"
ng-click="save($event)"
ng-disabled="!form.$valid"
class="btn check-icon">Save</button>
<button ng-switch-when="delete"
ng-click="save($event)"
ng-disabled="!form.$valid"
class="btn check-icon">Delete</button>
<button ng-switch-default
ng-click="save($event)"
ng-disabled="!form.$valid"
class="btn check-icon">Save</button>
</ng-switch>
<span role="button" ng-click="cancel($event)" class="x-icon">Cancel</span>
</div>
</div>

<div data-ng-bind-html="model.$viewValue.body"
data-ng-hide="editing"
class="body" />

<!-- Editing preview -->
<div ng-show="previewText" class="preview">
<h4>Preview</h4>
<div ng-bind-html="previewText" class="body" />
</div>

<!-- Share dialog -->
<div class="share-dialog" data-ng-show="!editing">
<div class="icon-input">
<div class="go-icon">
<a class="launch-icon show" href="{{shared_link}}" target="_blank"></a>
</div>
<div class="share-div">
<input class="share-text" type="text" ng-model="shared_link" readonly ng-blur="toggle()" />
</div>
</div>
</div>

<!-- Bottom control strip -->
<div class="magicontrols small" ng-hide="editing">
<span class="reply-count"
ng-pluralize=""
ng-show="thread.children.length && replies!='false'"
count="thread.flattenChildren().length"
when="{one: '1 reply', other: '{} replies'}" />
<a class="reply-icon show" href="" title="Reply" ng-click="reply($event)">Reply</a>
<!-- <span class="fave-icon" title="Favorite"
ng-click="favorite()" /> -->
<a class="share-icon show" href="" title="Share" ng-click="share($event)">Share</a>
<a class="clipboard-icon show" href="" title="Edit" ng-show="auth.update" ng-click="edit($event)">Edit</a>
<a class="x-icon show" href="" title="Delete" ng-show="auth.delete" ng-click="delete($event)">Delete</a>
<a class="flag-icon show" href="" title="flag" ng-hide="auth.delete" ng-click="flag($event)">Flag</a>

</div>
</form>

</script>
<script type="text/ng-template" id="editor.html">
<ul class="sliding-panels">
<li class="annotator-outer annotator-editor">
<div class="paper">
<div class="excerpt" ng-repeat="target in annotation.target">
<blockquote ng-bind="target.quote" ng-hide="target.showDiff" />
<blockquote ng-bind-html="target.trustedDiffHTML" ng-show="target.showDiff" />
<div class="small pull-right" ng-show="target.diffHTML">
<input type="checkbox" ng-model="target.showDiff" ng-click="$event.stopPropagation()"> Show differences</input>
</div>
</div>
<div ng-model="$parent.annotation" class="annotation" />
</div>
</li>
</ul>

</script>
<script type="text/ng-template" id="markdown.html">
<textarea ng-hide="readonly"
ng-click="$event.stopPropagation()"
ng-required="required" />
<div ng-bind-html="rendered" ng-show="readonly" />

</script>
<script type="text/ng-template" id="privacy.html">
<div class="dropdown">
<span name="privacy"
role="button"
class="dropdown-toggle"
ng-model="model.$viewValue"
data-toggle="dropdown">{{model.$viewValue}}</span>
<ul class="dropdown-menu" role="menu">
<li ng-repeat="p in levels">
<a href="" ng-click="model.$setViewValue(p)">{{p}}</a>
</li>
</ul>
</div>

</script>
<script type="text/ng-template" id="userPicker.html">
<form action=""
method="POST"
enctype="multipart/form-data"
accept-charset="utf-8">
<div class="dropdown" ng-show="model">
<span role="button"
class="dropdown-toggle"
data-toggle="dropdown"
>{{model.username}}<span class="provider"
>/{{model.provider}}</span></span>
<ul class="dropdown-menu pull-right"
role="menu">
<li ng-repeat="option in options"
ng-click="model = options[$index]"
>{{option.username}}/{{option.provider}}</li>
<li><a href="http://hypothes.is/contact/" target="_blank">Feedback</a></li>
<li><a href="/docs/help" target="_blank">Help</a></li>
<li><a href="/stream#?user={{model.username}}" target="_blank">My Annotations</a></li>
<li ng-click="model = null">Sign out</li>
</ul>
</div>
</form>

</script>
<script type="text/ng-template" id="viewer.html">
<ul class="sliding-panels">
<li>
<ul>
<li ng-mouseenter="focus(annotation)"
ng-mouseleave="focus()"
ng-repeat="annotation in annotations track by annotation.id"
class="stream-list"
ng-class="annotation.$emphasis && 'card-emphasis'"
>

<!-- Thread view -->
<div class="paper thread"
data-recursive=""
ng-class="collapsed && 'collapsed summary' || ''"
ng-mousedown="toggleCollapsedDown($event)"
ng-click="toggleCollapsed($event)"
ng-init="collapsed = true"
>
<a href="" class="threadexp"
title="{{collapsed && 'Expand' || 'Collapse'}}"
ng-show="$parent.annotation"
/>

<!-- Excerpts -->
<div class="excerpt"
ng-repeat="target in annotation.target"
ng-hide="collapsed">
<blockquote ng-bind="target.quote" ng-hide="target.showDiff" />
<blockquote ng-bind-html="target.trustedDiffHTML" ng-show="target.showDiff" />
<div class="small pull-right" ng-show="target.diffHTML">
<input type="checkbox" ng-model="target.showDiff" ng-click="$event.stopPropagation()"> Show differences</input>
</div>
</div>

<!-- Annotation -->
<div class="annotation"
name="annotation"
ng-model="$parent.annotation" />

<!-- Replies -->
<ul>
<li class="thread"
ng-class="collapsed && 'collapsed' || ''"
ng-click="toggleCollapsed($event)"
ng-repeat="annotation in annotation.reply_list"
ng-transclude
/>
</ul>
</div>
</li>
</ul>
</li>
</ul>

</script>
<script type="text/ng-template" id="page_search.html">
<ul class="sliding-panels">
<!-- Search -->
<li>
<ul>
<li ng-repeat="thread in search_annotations"
ng-mouseenter="focus(thread.message)"
ng-mouseleave="focus()"
class="stream-list summary"
ng-class="thread.message.$emphasis && 'card-emphasis'"
>
<!-- Thread view -->
<div data-recursive="" class="paper thread"
ng-mousedown="toggleCollapsedDown($event)"
ng-click="toggleCollapsed($event)"
ng-init="collapsed = ann_info.show_quote[thread.message.id]">
<!-- Annotation -->

<!-- Excerpts -->
<div class="excerpt"
ng-repeat="target in thread.message.target"
ng-hide="collapsed">
<blockquote ng-bind-html="target.highlightQuote" ng-hide="target.showDiff" />
<blockquote ng-bind-html="target.trustedDiffHTML" ng-show="target.showDiff" />
<div class="small pull-right" ng-show="target.diffHTML">
<input type="checkbox" ng-model="target.showDiff"> Show differences</input>
</div>
</div>

<!--"Load more replies" label for the top -->
<a href=""
class="load-more"
ng-show="ann_info.more_top[thread.message.id] && ann_info.more_top_num[thread.message.id] <2"
ng-click="clickMoreTop(thread.message.id, $event)">
load 1 more reply
</a>

<a href=""
class="load-more"
ng-show="ann_info.more_top[thread.message.id] && ann_info.more_top_num[thread.message.id] >1"
ng-click="clickMoreTop(thread.message.id, $event)">
load {{ann_info.more_top_num[thread.message.id]}} more replies
</a>


<div class="detail annotation"
name="annotation"
ng-model="$parent.thread.message"
mode="search"
replies="{{!ann_info.more_bottom[thread.message.id]}}"
ng-show="$parent.ann_info.shown[thread.message.id] == null || $parent.ann_info.shown[thread.message.id]"/>

<!--"Load more replies" label for the bottom -->
<a href=""
class="load-more"
ng-show="ann_info.more_bottom[thread.message.id] && ann_info.more_bottom_num[thread.message.id]<2"
ng-click="clickMoreBottom(thread.message.id, $event)">
load 1 more reply
</a>

<a href=""
class="load-more"
ng-show="ann_info.more_bottom[thread.message.id] && ann_info.more_bottom_num[thread.message.id] >1"
ng-click="clickMoreBottom(thread.message.id, $event)">
load {{ann_info.more_bottom_num[thread.message.id]}} more replies
</a>

<!-- Replies -->
<ul>
<li class="thread"
ng-class="collapsed && 'collapsed' || ''"
ng-mousedown="toggleCollapsedDown($event)"
ng-click="toggleCollapsed($event)"
ng-repeat="thread in thread.children"
ng-transclude>
<a href=""
class="threadexp"
title="{{collapsed && 'Expand' || 'Collapse'}}"
ng-mousedown="toggleCollapsedDown($event)"
ng-click="toggleCollapsed($event)"
ng-show="ann_info.shown[thread.message.id] && !ann_info.more_top[thread.message.id]"
/>
</li>
</ul>
</div>
</li>
</ul>
</li>
</ul>

</script>
<script type="text/ng-template" id="notification.html">
<div class="pull-right">
<div title="Close" class="cancel-icon" ng-click=model.$viewValue.close()></div>
</div>
<div ng-click="model.$viewValue.callback()"
class="pull-left notif-text">
{{model.$viewValue.text}}
</div>

</script>
<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content ui-corner-all VS-interface" id="ui-id-1" tabindex="0" style="display: none;"></ul><div class="annotator-notice"></div></div>