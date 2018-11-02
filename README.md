# WP Clone

Utility to clone a live Wordpress installation from a remote host
to the local system incl. database dump. Boots the clone into
Docker containers to run it locally (incl. xdebug step debugger)

## Usage

~~~
bin/wp-clone [<ssh-host>:<path>] [<target>]
~~~

By default wp-clone clones into a subdirectory with the name of
the ssh-host into the *sites* directory, e.g. `sites/ssh-host`.

After a host has been cloned the first time into a target, the
last target is stored and both arguments are now optional.

## Requirements

The script requires a *bash* or compatible shell, *ssh* access
to the remote host, *rsync* for incremental file transfer, *scp*
for file transfer, *docker* for local service containers and
git for configuration management.

The integration with the local network is quite rough and has a
lot of room for improvements:

By default it will use port 80 for HTTP access to the instance.
The hostname will be "localhost".

The SSH-Host must be properly configured for non-interactive use,
that is, wp-clone will not ask for a password (batch mode), so
take care the ssh key manager (*ssh-agent*) is properly set up
for the host.
