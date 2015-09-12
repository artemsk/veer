<blockquote>
<p>Latest available: <strong>{{ $latest }}</strong></p>
<p>Installed: <strong>{{ $current }}</strong></p>
@if($latest != $current)
<!-- <input type="hidden" name="actionButton" value="updateVeerAppToLatestVersion">
<button type="submit" class="btn btn-default" name="action" value="updateVeerApp" data-resultdiv="#compareVersions">Update</button> -->
@endif
</blockquote>
