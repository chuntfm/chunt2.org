<?php

class EventPage extends Page
{

    /**
     * Determines if the event page is readable by the current user.
     *
     * This method overrides the default isReadable method of the Page class in Kirby.
     * It checks the role of the current user and the creator's email of the event.
     * If the user is an 'api' user, a 'moderator', an admin, or the creator of the event, the method returns true.
     * Otherwise, it returns false.
     *
     * @return bool True if the event page is readable by the current user, false otherwise.
     */
    public function isReadable(): bool
    {
        $user = $this->kirby()->user();
        $managedEmails = $user->managedemails()->toArray();

        // extend array by user email
        $managedEmails[] = $user->email();

        if (in_array($user->role(), ['api', 'moderator']) || $user->isAdmin() || in_array($this->creatormail(), $managedEmails)) {
            return true;
        } else {
            return false;
        }
    }

    public function startDateTime(): DateTime
    {
        $timestamp = $this->startTimestampUTC()->toTimestamp();
        $date = new DateTime("@$timestamp"); // prepend '@' to specify a Unix timestamp
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date;
    }

    public function endDateTime(): DateTime
    {
        $timestamp = $this->endTimestampUTC()->toTimestamp();
        $date = new DateTime("@$timestamp"); // prepend '@' to specify a Unix timestamp
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date;
    }

}

?>
