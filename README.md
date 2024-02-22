# Pretix attende list plugin for WordPress

This WordPress plugin publicly displays the attendance list of participants to a Pretix event who have explicitly agreed to be displayed.


## Compatibility

This plugin supports both single-time events and event series.

## Usage

To start using the plugin, first you have to configure your Pretix API URL, Pretix API token, and the organization name.

Adding the list to a web page is done via the specific shortcode `[pretix_attendee_list]`

### Parameters

 - Required parameters
	- `sona_name_question_identifier` - the ID of the question relating to the participant's name
	- `permission_question_identifier` - the ID of the question relating to the explicit agreement
	- `event` - the ID of the event
- Optional parameters
	- `subevent` - the ID of the specific event series, in case that it is left empty, the plugin will automatically display the list of the participants from the closest series, date-wise, by default, it is set to `null`
	- `is_singular_event` - In case of a singular event, this must be set to `True`, by default, it is set to `False`


