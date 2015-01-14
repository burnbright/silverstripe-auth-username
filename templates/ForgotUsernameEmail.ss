<p><% _t('HELLO', 'Hi') %> there,</p>

<p></p><strong>Usernames associated with $Email:</strong></p>
<ul>
<% loop Members %>
<li>$Username</li>
<% end_loop %>
</ul>