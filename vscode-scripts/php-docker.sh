#!/bin/bash
docker-compose exec -T web-server php "$@"
