function toggleCompanyInfo()
{
	$(".org_info").toggle();
	
	if($("#rep_org").is(":checked")){
		//Add '-required' to each element's name
		$("#org_phone").attr("name", "phone-required");
		$("#org_position").attr("name", "position-required");
		$("#org_url").attr("name", "url-required");
		$("#company").attr("name", "company-required");
	}
	else
	{
		//remove '-required' from each element's name
		$("#org_phone").attr("name", "phone");
		$("#org_position").attr("name", "position");
		$("#org_url").attr("name", "url");
		$("#company").attr("name", "company");
	}
}