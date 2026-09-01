# Coffee Shortcut

Lets you choose the keyboard shortcut that opens the Coffee module's
search box, instead of being stuck with its default alt + D.

## Why I built this

I use Coffee on my own admin pages, and I like it a lot, it saves a lot
of clicking around menus. But its default shortcut is alt + D, and in my
browser, alt + D has always meant one thing: jump to the address bar.
Coffee took that shortcut over completely, so every time I reached for
my browser's own shortcut out of habit, Coffee opened instead.

At first I thought of it as just an annoyance I would get used to. Then
I realized it is more serious than that for a lot of people. Anyone who
navigates mostly by keyboard, or who uses a screen reader, tends to rely
on a small set of dependable browser shortcuts to move around quickly.
Alt + D to reach the address bar is one of the most common ones. If a
module quietly takes that away with no warning, it is not just a small
inconvenience, it breaks a habit someone may genuinely depend on.

I looked for a setting inside Coffee to change or turn off that shortcut
and did not find one, so I built this instead. It does not touch a
single line of Coffee's own code. It sits alongside Coffee and lets you
either free alt + D back to the browser, or bind your own shortcut of
choice to open Coffee. Once it was working well on my own project, I
decided to package it properly and share it, in case someone else runs
into the same wall I did.

## What this module does

Adds a settings page at Configuration > User interface > Coffee shortcut
(`/admin/config/user-interface/coffee-shortcut`), gated behind Coffee's
own "Administer Coffee" permission, with two independent options:

- Block the default alt + D shortcut, freeing it for the browser. Coffee's
  own built in alt + K keeps working either way.
- Bind an additional custom shortcut, any combination of Alt, Shift, Ctrl
  and Meta plus a single letter or digit, that opens Coffee.

Both options are applied by a small script that reacts to key presses in
the browser and, when needed, calls Coffee's own public
`DrupalCoffee.coffee_show()` function to open it. Coffee's own files are
never modified, read, copied or overridden, so this module keeps working
across Coffee updates without any changes on this end.

## Requirements

- Drupal core 11.2 or later.
- The Coffee module (`drupal/coffee`).

## Installation

This module is not on Packagist or drupal.org, so Composer does not know
where to find it until you tell it. Run this once, from your project's
root folder, to register this repository:

```
composer config repositories.coffee-shortcut vcs https://github.com/AbdullahZubair/coffee-shortcut
```

That adds an entry to your project's `composer.json` under
`repositories`, pointing at this GitHub repository. You only need to do
this once per project, not once per version.

Then require and enable the module as usual:

```
composer require abdullahzubair/coffee-shortcut
drush en coffee_shortcut -y
drush cr
```

If you are not installing it through Composer, you can also place the
module folder directly in `modules/custom/coffee_shortcut` and enable it
the same way with `drush en`.

Then visit Configuration > User interface > Coffee shortcut to set the
shortcut for this site.

## Verifying it works

After enabling the module and setting a shortcut, a quick way to check
it took effect:

- With "Block the default alt + D shortcut" turned on, press alt + D on
  any page. Coffee should not open, and your browser's own alt + D
  behavior, such as moving focus to the address bar, should happen
  instead.
- Coffee's own alt + K should still open Coffee normally either way.
- If you set a custom shortcut, pressing that combination should open
  Coffee, the same as alt + K does.
- Log out, or use a user without the "Access Coffee" permission, and
  confirm none of this changes anything for that user, since the
  module only acts for users who can use Coffee in the first place.
