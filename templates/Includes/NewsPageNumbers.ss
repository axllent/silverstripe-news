<% if $PaginatedArticles.MoreThanOnePage %>
	<p class="newspages">
		<% if $PaginatedArticles.NotFirstPage %>
			<a class="prev" href="$PaginatedArticles.PrevLink">&lt;&lt;Prev</a>
		<% else %>
			&lt;&lt;Prev
		<% end_if %>
			<% loop $PaginatedArticles.Pages %>
				<% if $CurrentBool %>
					$PageNum
				<% else %>
					<% if $Link %>
						<a href="$Link">$PageNum</a>
					<% end_if %>
				<% end_if %>
			<% end_loop %>
			<% if $PaginatedArticles.NotLastPage %>
				<a class="next" href="$PaginatedArticles.NextLink">Next&gt;&gt;</a>
			<% else %>
				Next&gt;&gt;
		<% end_if %>
	</p>
<% end_if %>