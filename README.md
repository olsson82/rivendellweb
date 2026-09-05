# Rivendell Web Broadcast
![Skärmbild från 2024-04-30 17-57-25](https://github.com/olsson82/rivendellweb/assets/122672087/c9e12210-5af5-4893-bac2-af8428f7f04d)

Web system for manage Rivendell Radio Automation created by Andreas Olsson to help manage rivendell remote with the most function.

The goal with this is to be able to do the most things from this system.

**This is still in development so there can be things that not works, contain bugs and things can be changed. You use it at your own risk. Do not use it on a live rivendell machine unless you know what you are doing, and that you know it will not brake your system. Before you do anything. Read the [**Documentation**](https://docs.rivendellwebsys.com/)**

**Please be advice that this system use BOTH Rivendells API and also do direct changes in the database**

## Log generator
There is an Log generator for testing only as it under heavy development. Do not run it on a live production server yet. There are not yet possible to use external scheduler. Needs to be enable in the settings, also you need to setup crontab: * * * * * curl -s http://localhost/api/generator.php > /dev/null

## Multitrack Editor
There is an multitrack editor in voicetracking for testing. Not recommended to use it on live production server yet. This need to be activated in system settings. Read documents for more info.

## Discussions
If you have any suggestions or just wants to discuss about the system. Please use the [**Discussions**](https://github.com/olsson82/rivendellweb/discussions). For bugs, use the issues.

## Some features in this system
- **Library:** Manage your library just like in Rivendell.
- **Logs:** Create logs, record voicetracks.
- **Log Manager:** Manage your events, clocks and grids. With special feature like grid layout to save multiple grids templates.
- **RDCatch:** Manage your automation
- **Admin features:** Some admin features can be manage.

## The project
This project started after i fixed some bugs in an old script by Brian McGlynn for to use with Rivendell Radio Automation. The original script can be found here: https://github.com/bpm1992/rivendell/tree/rdweb/web/rdphp

But i needed something with more user friendly layout with more function. So i started to work on this. Im not an professional programmer, so you know.

## How to use
To use this you need a machine that have Rivendell installed. This system can not work without it. It uses the user created in RDAdmin. Run it behind a reverse proxy with https support. This is develop for **Version 4** of Rivendell and has not tested with older versions.

The system has a installer, and no files need to be modified. Read the installation section in the documentation.

[**See the documentation for more info**](https://docs.rivendellwebsys.com/)

## Translation
[![Crowdin](https://badges.crowdin.net/rivendell-web-broadcast/localized.svg)](https://crowdin.com)

My language is Swedish, so the english can be non correct, please report it.

Contribute to the system and translate to your language. 

Go to [Crowdin](https://crowdin.com/project/rivendell-web-broadcast) and start translate.

Base language is **English**

Translated to:
* Swedish by Andreas Olsson

## Discord
Join the [Discord](https://discord.gg/ztMfMPngC5) channel to help me with ideas and give feedback about the system.

## Special thanks to
[Saugi](https://github.com/zuramai/mazer) the creator for Mazer bootstrap theme.

