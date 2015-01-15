<div class="comment-field" ng-show="user.id">
    <form name="add-comment-form" ng-submit="commentSubmit(comment)">
        <input ng-model="comment.text" id="doc-comment-field" type="text" class="form-control centered" placeholder="{{ trans('messages.addacomment') }}" required />
        {{-- <button class="btn btn-primary">Add Comment</button> --}}
    </form>    
</div>