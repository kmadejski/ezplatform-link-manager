#!/usr/bin/env sh
echo 'Translation extraction';
cd ../../..;
# Extract string for default locale
echo '# Extract EzPlatformLinkManagerBundle';
./app/console translation:extract en -v \
  --dir=./vendor/ezsystems/ezplatform-link-manager \
  --exclude-dir=Tests \
  --exclude-dir=vendor \
  --output-dir=./vendor/ezsystems/ezplatform-link-manager/src/bundle/Resources/translations \
  --keep
  "$@"

echo '# Clean file references';
sed -i "s|>.*/ezplatform-link-manager/|>|g" ./vendor/ezsystems/ezplatform-link-manager/src/bundle/Resources/translations/*.xlf

cd vendor/ezsystems/ezplatform-link-manager;
echo 'Translation extraction done';
