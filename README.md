# Pretix attendee list plugin for WordPress

This WordPress plugin publicly displays the attendance list of participants to a Pretix event who have explicitly agreed to be displayed.


## Compatibility

This plugin supports both single-time events and event series. Requires Wordpress with PHP 8.1

## Usage

To start using the plugin, archive all .php files into a .zip archive, and then it can be installed via the WordPress interface. 

### Configuration 

Global configuration for the plugin are done via the `Attendee List settings` button on the admin dashboard.

The following parameters must be configured in order for the plugin to function properly:
- `API URL` - The Pretix API URL (e.g. `https://pretix-instance.example.org/api/v1`
- `API Token` - The Pretix API Token (can be obtained at the team configuration pages of the organizer)
- `Organizer` - The organizer name
  
Adding the list to a web page is done via the specific shortcode `[pretix_attendee_list]`

### Parameters

 - Required parameters
	- `sona_name_question_identifier` - the ID of the question relating to the participant's name
	- `permission_question_identifier` - the ID of the question relating to the explicit agreement
	- `event` - the ID of the event
   
- Optional parameters
	- `subevent` - the ID of the specific event series, in case that it is left empty, the plugin will automatically display the list of the participants from the closest series, date-wise. By default, it is set to `null`
	- `is_singular_event` - In case of a singular event, this must be set to `True`. By default, it is set to `False`


