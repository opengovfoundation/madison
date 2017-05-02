<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Doc as Document;
use App\Models\Sponsor;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the doc.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Doc  $document
     * @return mixed
     */
    public function view(User $user, Document $document)
    {
        if (in_array(
            $document->publish_state,
            [Document::PUBLISH_STATE_PUBLISHED, Document::PUBLISH_STATE_PRIVATE]
        )) {
            return true;
        }

        if (
            $document->publish_state == Document::PUBLISH_STATE_UNPUBLISHED
            && $this->viewManage($user, $document)
        ) {
            return true;
        }

        return false;
    }

    public function viewManage(User $user, Document $document)
    {
        return $document
            ->sponsors
            ->filter(function ($sponsor) use ($user) {
                return $sponsor->hasMember($user->id);
            })
            ->isNotEmpty()
            ;
    }

    /**
     * Determine whether the user can create docs on behalf of the sponsor.
     *
     * @param  \App\Models\User     $user
     * @param  \App\Models\Sponsor  $sponsor
     * @return mixed
     */
    public function create(User $user, Sponsor $sponsor)
    {
        return $sponsor && $sponsor->isActive() && $sponsor->userCanCreateDocument($user);
    }

    /**
     * Determine whether the user can update the doc.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Doc  $document
     * @return mixed
     */
    public function update(User $user, Document $document)
    {
        return $document
            ->sponsors
            ->filter(function ($sponsor) use ($user) {
                return $sponsor->userCanCreateDocument($user);
            })
            ->isNotEmpty()
            ;
    }

    /**
     * Determine whether the user can delete the doc.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Doc  $document
     * @return mixed
     */
    public function delete(User $user, Document $document)
    {
        return $this->update($user, $document);
    }

    public function restore(User $user, Document $document)
    {
        if ($document->publish_state === Document::PUBLISH_STATE_DELETED_ADMIN) {
            return $user->isAdmin();
        }
        return $this->delete($user, $document);
    }

    public function moderate(User $user, Document $document)
    {
       return $this->viewManage($user, $document);
    }
}
