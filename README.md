# iCalendar for Ninja Forms

[![Latest Stable Version](https://poser.pugx.org/soderlind/icalendar-ninja-forms/v)](//packagist.org/packages/soderlind/icalendar-ninja-forms)

## Description

Add an iCalendar to your Ninja Forms.

## Installation

Prerequisite:

- PHP 7.4-8.x
- [Ninja Forms](https://wordpress.org/plugins/ninja-forms/)

You know the drill:

1. [Download the plugin](https://github.com/soderlind/icalendar-ninja-form/archive/refs/heads/main.zip)
1. Upload the plugin via `Plugins->Add New->Upload`
1. Activate the plugin.
1. Use Ninja Forms to add the iCalendar.

## Use

### Per event form, add the action:

<img src=".wordpress-org/add-icalendar.gif" />

### Add event date etc:

<img src=".wordpress-org/icalendar-event.png" />

### Add link to Success Message, Email Confirmation etc:

- **Link** (`{ical:link}`), adds a `<a href=".../event-xyz.ics">Add the event to your calendar</a>` link. The "Add the event to your calendar" text can be changed in iCalendar settings.
- **URL** (`{ical:url}`), adds `http[s]://yoursite.tld/event-xyz.ics`

<img src=".wordpress-org/add-merge-tag.gif" />

### Conditional Logic

Require [Ninja Forms Conditional Logic](https://ninjaforms.com/extensions/conditional-logic/) to be enabled.

Per condition

- add an iCalendar
- add a response.

See example below

<img src=".wordpress-org/conditional-logic-icalendar.gif" />

### Email with iCalendar link

<img src=".wordpress-org/email.png" />

### Example calendar.ics:

```
BEGIN:VCALENDAR
PRODID:-//eluceo/ical//2.0/EN
VERSION:2.0
CALSCALE:GREGORIAN
BEGIN:VEVENT
UID:1f09c1aca9cca3bcee4b588ab0bc624d
DTSTAMP:20240322T082413Z
SUMMARY:Launch Party!
DESCRIPTION:Welcome to our Launch Party!
URL:http://domain.local/party/
DTSTART:20240606T090000Z
DTEND:20240606T150000Z
LOCATION:Vippa\, Oslo
GEO:59.901864;10.741167
X-APPLE-STRUCTURED-LOCATION;VALUE=URI;X-ADDRESS=Vippa\, Oslo;X-APPLE-RADIUS
 =49;X-TITLE=:geo:59.901864,10.741167
ORGANIZER:mailto:party@domain.local
END:VEVENT
END:VCALENDAR
```

<img src=".wordpress-org/calendar-example.png">

## See also

I've created a [date range](https://github.com/soderlind/date-range-ninja-forms) add-on for Ninja Forms.

## Changelog

### 2.2.0

- Create the calendar using [Eluceo\iCal](https://github.com/markuspoerschke/iCal)
  - multiline message
  - link to event page
  - add event location, inclusive latitude and longitude
  - attach calendar to email confirmation
- Update translation file.
  - Add Norwegian translation
- Update dependencies
- Housekeeping

### 2.1.1

- PHPStan it.

### 2.1.0

- Enable conditional logic for the iCalendar.

### 2.0.0

- Note, this is a breaking change. You can now select a date range and a time range.

### 1.3.4

- Remove incompatible attribute.

### 1.3.3

- Rename method in `include/js/actions.js`

### 1.3.2

- Set default title in vcalendar
- Mark mandatory settings

### 1.3.0

- Refactor away from anonymous class.

### 1.2.0

- Refactor, move create VCALENDAR to card() in class Invitation.

### 1.1.4

- Update translation file.

### 1.1.3

- Rename methods in JavaScript
- Lint JavaScript using [Rome](https://rome.tools/#installation-and-usage)

### 1.1.2

- Remove wp_localize_script
- Rename style object

### 1.1.1

- Add "now" as default date and time.

### 1.1.0

- Add date and time picker

### 1.0.0

- Initial release.

## Credits

iCalendar for Ninja Forms uses the following third-party resources:

- [Eluceo\iCal](https://github.com/markuspoerschke/iCal) by Markus Poerschke, licensed under the MIT License.
- [Openstreetmap](https://www.openstreetmap.org) for the calendar location.
- [Enhanced WP Mail Attachments](https://gist.github.com/thomasfw/5df1a041fd8f9c939ef9d88d887ce023/) by Thomas F. Watson.

## Copyright and License

iCalendar for Ninja Forms is copyright 2021 Per Søderlind

iCalendar for Ninja Forms is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

iCalendar for Ninja Forms is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with the Extension. If not, see http://www.gnu.org/licenses/.
