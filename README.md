# Plugin-Discord-Auth-Azuriom

This Azuriom plugin allow users to authenticate thought Discord OAuth2 API.

## Installations

### Add a login via discord button in your template 

Example : 
`ressources/themes/carbon/view/elements` near `<!-- Authentication Links -->`
```html
@plugin('discord-auth') {{-- if plugin discord-auth is enabled --}}
    @guest  {{-- if user is not authenticated --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('discord-auth.login') }}">
                {{ trans('discord-auth::messages.login_via_discord') }}
            </a>
        </li>
    @endguest
@endplugin
```

You can also use the admin panel to add a navigation button, but it may be a bit less aesthetic 
`Admin panel -> Navigation` 

###  Register a discord app and fill credentials
* Register a Discord application here https://discord.com/developers/applications
* Go to plugin's settings and fill client_id and client_secret

### Update depedencies
Go to `plugin/discord-auth` and run `composer update`
