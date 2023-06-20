# Deepl.com integration for Nextcloud

This app integrates with the translation API of Nextcloud server to offer translation services through deepl. Currently this is available in text.

## Ethical AI Rating
### Rating: 🔴

Negative:
* the software for training and inferencing of this model is proprietary, limiting running it locally or training by yourself
* the trained model is not freely available, so the model can not be ran on-premises
* the training data is not freely available, limiting the ability of external parties to check and correct for bias or optimise the model’s performance and CO2 usage.

Learn more about the Nextcloud Ethical AI Rating [in our blog](https://nextcloud.com/blog/nextcloud-ethical-ai-rating/).

## Configuring

```
occ config:app:set integration_deepl apikey --value="apikey"
```
