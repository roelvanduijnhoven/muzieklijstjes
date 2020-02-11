GIT_HASH=`git rev-parse HEAD`

docker build -t roelvd/muzieklijstjes.nl:$GIT_HASH -t roelvd/muzieklijstjes.nl:latest .
docker push roelvd/muzieklijstjes.nl:$GIT_HASH
docker push roelvd/muzieklijstjes.nl:latest

kubectl delete deployment muzieklijstjes-deployment
kubectl apply -f muzieklijstjes.yml