$Content
$Form
<% include NewsPageNumbers %>
<% loop $PaginatedArticles %>
	<article class="articlesnippit">
		<% if $Thumbnail %>
			<a href="$Link" class="thumbnail">$Thumbnail.CroppedImage(100,100)</a>
		<% end_if %>
		<div class="articledate">
			<span class="day">$Date.DayOfMonth</span> <span class="month">$Date.ShortMonth</span> <span class="year">$Date.Year</span>
		</div>
		<h3><a href="$Link">$Title</a></h3>
		$Content.FirstParagraph(0)
		<p class="readarticle"><a href="$Link">Read article &gt;&gt;</a></p>
	</article>
<% end_loop %>
<% include NewsPageNumbers %>