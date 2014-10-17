## Meta page

(This is an experiment: to see how I can work on the wiki in a controlled way, and get feedback along the way --- denise)

This page is a meta page for the wiki:
* Describe the use and evolution of the wiki
* Features/content needed for the wiki
* A place to "try out" content before migrating it to the "regular" wiki and/or code docs.

### Where documentation belongs
Aspects of documentation that follow / change with code belong in the code tree,  versioned in Github.  Aspects of documentation have a different lifecycle than the code belong in this wiki.  Some examples:
* How to install, run, test, etc.: in Github (which is also standard convention).
* How to contribute, where to find people, etc.,: in Wiki
* Code documentation: in Github.
* Design principles, conceptual architecture, etc: in Github. (? Could also make a case it belongs in the Wiki, as it may evolve differently.)
* Product roadmap, design discussions, design rationale: in Wiki.

Sometimes these will be fuzzy, especially when the result of design discussion is design documentation.   But it helps to have a place to start.

Note: since the wiki isn't fork-able, for now I will put all documents in the docs directory on Github, and we can sort them out later.

### Documentation/Wiki TODO list
* "Conceptual architecture" : what are the nouns and verbs --- the big moving parts?  Might also (or instead) do this in a "user story"/"use case" form:  who would interact with the system and what would they do?
* Roadmap in big terms (supplement the existing detailed roadmap).
* Extended installation/usage instructions:
** How do you go about creating a madison instance beyond just installing the code?
** How do you use it once you have it?
** Where are intended customization points?
* Invitation: how can others find useful things to do.

