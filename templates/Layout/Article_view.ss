<article class="articlestory">
	<% if $Article.Thumbnail %>
		<div class="thumbnail">$Article.Thumbnail.SetWidth(400)</div>
	<% end_if %>
	<div class="articledate">
		<span class="day">$Article.Date.DayOfMonth</span>
		<span class="month">$Article.Date.ShortMonth</span>
		<span class="year">$Article.Date.Year</span>
	</div>
	<h1>$Article.Title</h1>
	$Article.Content
</article>