import axios from "axios";
import { useState, useEffect } from "react";

export default function Connections({ type }) {

  const [connections, setConnections] = useState(null);

  useEffect(() => {
    const loadConnections = async () => {
      
      const { data } = await axios.get(`/frontend/follow/${type}/bitcoiner`);
      setConnections(data[type].data);

      console.log(data);
    }

    loadConnections();
  }, []);

  return (
    <div>
      <div>List of { type === 'followers' ? 'Followers' : 'Following' }</div>
      { connections?.map(connection => (
        <div>
          { connection.name }
          <img src={connection.twitter_profile_image_url} style={{ width: 32, height: 32 }} />
        </div>
      ))}
    </div>
  );
}
