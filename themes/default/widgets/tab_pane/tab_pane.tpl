<div class="tab_pane" id="{id}" flexy:raw="{tab_pane_properties:h}">
<ul class="tabs">
{foreach:tabs,tab}
<li>
<a href="#" flexy:if="tab[active]" flexy:raw="{tab[actions]:h}" class="active">{tab[label]}</a>
<a href="#" flexy:if="!tab[active]" flexy:raw="{tab[actions]:h}">{tab[label]}</a>
</li>
{end:}
</ul>
<div class="tab_pane_page" style="{tab_pane_height}">
{active_page:h}
</div>
</div>