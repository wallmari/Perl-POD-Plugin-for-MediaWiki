# What?

This is a MediaWiki extension that allows you to embed Perl POD into wiki
pages, which could simplify system documentation by having all information
in a single documentation system.

# Why?

It's hard to argue that, thanks to Wikipedia, MediaWiki is possibly the best
known wiki engine around. There's plenty of information to help beginners to
get to grips with creating and editing articles, there are plenty of existing
extensions to add features to MediaWiki, and there's an internal API to
extend MediaWiki if necessary.

POD has long been the preferred way of documenting Perl code - the inline
nature of it means it's easily found, easy to keep updated, and the simple
markup used lends itself easily to conversion to other formats. For an
experienced Perl programmer, POD is usually the first place that's checked for
information.

However, if the Perl code forms part of a larger system (and particularly if
the system uses several different coding languages for different parts) it
may be more convenient to include the Perl documentation in a format more
familiar to users of other systems. Keeping the central information store in
sync with the POD could end up with a complex web of trigger scripts to
convert and update pages as the POD changes.

# How?

Once this extension is installed and enabled, including POD in a page is as
simple as adding a new "magic word" to the page:

{{#pod:Scalar::Util}}

The POD for the module will be rendered into MediaWiki markup, which is then
rendered by MediaWiki into the final output. The process of POD -> wiki
markup -> HTML instead of just POD -> HTML is deliberate, as it allows
MediaWiki to format the documentation in the same manner as the rest of the
wiki.

This extension uses two system utilities to to the hard work:

* perldoc   (perl-doc in Debian)
* Pod::Simple::Wiki   (libpod-simple-wiki-perl in Debian)

If perldoc isn't installed, the extension will just return an error.
If Pod::Simple::Wiki (particularly, Pod::Simple::Wiki::Mediawiki) isn't
installed, the output will be undefined (but not pretty)

# Who?

The blame falls squarely on:

* Richard Wallman <richard.wallman@bossolutions.co.uk>

# TODO

* Add an option to change the include path, allowing for local::lib installs
* Handle PHP "safe mode" as best as possible
