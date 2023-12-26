FROM busybox:1.36

COPY . /data

EXPOSE 9501

ENTRYPOINT ["sh", "/data/entrypoint.sh"]

WORKDIR /data