<div class="comment-field" ng-show="user.id">
    <form name="add-comment-form" ng-submit="commentSubmit()">
        <input ng-model="comment.content" id="doc-comment-field" type="text" class="form-control centered" placeholder="Add a comment" required />
        {{-- <button class="btn btn-primary">Add Comment</button> --}}
    </form>    
</div>