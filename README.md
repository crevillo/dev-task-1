# Dev task 1

In a eZ Platform 3.2 installation this can be solved adding this files to it. 

They are two suscribers listening to the `PublishVersionEvent`. Both performs checks to not do anything
in case the edited content is not an article or is not in eng-GB

Asummed here that the fre-FR has been added already to the eZ Platform install. 

The `SendContentToInfoExternalAPI` uses symfony Http Client to perform the query to the non existing point. 

This client is not installed in a eZ Platform clean install, so a `composer symfony/http-client` would be needed here. 

